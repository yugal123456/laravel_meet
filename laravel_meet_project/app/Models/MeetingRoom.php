<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailable($startTime, $duration, $requiredMembers)
    {
        if ($requiredMembers > $this->capacity) {
            return false;
        }
        $startTime = Carbon::parse($startTime);

        $endTime = (clone $startTime)->addMinutes($duration);

        return !$this->bookings()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('start_time', '<', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where(function ($sq) use ($startTime) {
                          $sq->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) > ?', [$startTime]);
                      });
                });
            })
            ->exists();
    }
}
