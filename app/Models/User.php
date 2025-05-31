<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'login_attempts',
        'blocked_until',
        'subscription_type',
        'daily_bookings_limit',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'blocked_until' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function isBlocked()
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

    public function incrementLoginAttempts()
    {
        $this->increment('login_attempts');

        if ($this->login_attempts >= 3) {
            $this->blocked_until = now()->addHours(24);
            $this->save();
        }
    }

    public function resetLoginAttempts()
    {
        $this->login_attempts = 0;
        $this->blocked_until = null;
        $this->save();
    }

    public function getTodayBookingsCount()
    {
        return $this->bookings()
            ->whereDate('created_at', today())
            ->count();
    }

    public function canBookMeeting()
    {
        return $this->getTodayBookingsCount() < $this->daily_bookings_limit;
    }
}
