<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'upcoming');
        $perPage = $request->input('per_page', 10);

        $bookings = $request->user()
            ->bookings()
            ->with('meetingRoom')
            ->when($filter === 'upcoming', function ($query) {
                return $query->upcoming();
            })
            ->when($filter === 'past', function ($query) {
                return $query->past();
            })
            ->latest('start_time')
            ->paginate($perPage);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'meeting_name' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|in:30,60,90',
            'number_of_members' => 'required|integer|min:1',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
        ]);

        $user = $request->user();

        if (!$user->canBookMeeting()) {
            throw ValidationException::withMessages([
                'booking' => ['You have reached your daily booking limit.'],
            ]);
        }

        $room = MeetingRoom::findOrFail($request->meeting_room_id);

        if ($request->number_of_members > $room->capacity) {
            throw ValidationException::withMessages([
                'number_of_members' => ['The number of members exceeds the room capacity.'],
            ]);
        }

        if (!$room->isAvailable($request->start_time, $request->duration, $request->number_of_members)) {
            throw ValidationException::withMessages([
                'meeting_room_id' => ['The selected room is not available for the specified time.'],
            ]);
        }

        $booking = $user->bookings()->create($request->all());

        return response()->json([
            'message' => 'Meeting room booked successfully',
            'booking' => $booking->load('meetingRoom'),
        ], 201);
    }

    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date|after:now',
            'duration' => 'required|in:30,60,90',
            'number_of_members' => 'required|integer|min:1',
        ]);

        $rooms = MeetingRoom::where('is_active', true)
            ->where('capacity', '>=', $request->number_of_members)
            ->get()
            ->filter(function ($room) use ($request) {
                return $room->isAvailable(
                    $request->start_time,
                    $request->duration,
                    $request->number_of_members
                );
            })
            ->values();

        return response()->json($rooms);
    }
}
