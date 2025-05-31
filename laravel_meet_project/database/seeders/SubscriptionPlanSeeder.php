<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'code' => 'basic',
                'daily_booking_limit' => 5,
                'price' => 9.99,
            ],
            [
                'name' => 'Advance Plan',
                'code' => 'advance',
                'daily_booking_limit' => 7,
                'price' => 19.99,
            ],
            [
                'name' => 'Premium Plan',
                'code' => 'premium',
                'daily_booking_limit' => 10,
                'price' => 29.99,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
