<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $rooms = Room::all();
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:available,occupied,maintenance'
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    }
}
