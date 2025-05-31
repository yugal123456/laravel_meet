<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return response()->json($plans);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
        $user = $request->user();

        // Simulate payment process
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'amount_paid' => $plan->price,
            'payment_status' => 'completed',
            'transaction_id' => Str::uuid(),
        ]);

        $user->update([
            'subscription_type' => $plan->code,
            'daily_bookings_limit' => $plan->daily_booking_limit,
        ]);

        return response()->json([
            'message' => 'Subscription purchased successfully',
            'subscription' => $subscription->load('subscriptionPlan'),
        ]);
    }

    public function mySubscription(Request $request)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->with('subscriptionPlan')
            ->where('payment_status', 'completed')
            ->where('end_date', '>', now())
            ->latest()
            ->first();

        return response()->json($subscription);
    }
}
