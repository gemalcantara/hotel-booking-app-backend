<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Rules\PromoCodeRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Constructor to apply authentication middleware
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the bookings.
     * 
     * @OA\Get(
     *     path="/api/bookings",
     *     operationId="getBookingsList",
     *     tags={"Bookings"},
     *     summary="Get list of active bookings",
     *     description="Returns paginated list of bookings with room details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="bookings", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $bookings = Booking::with('room')
            ->latest()
            ->paginate(10);

        return response()->json([
            'bookings' => $bookings
        ]);
    }

    /**
     * Store a newly created booking in storage.
     * 
     * @OA\Post(
     *     path="/api/bookings",
     *     operationId="storeBooking",
     *     tags={"Bookings"},
     *     summary="Create a new booking",
     *     description="Creates a new booking with validation",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Booking data",
     *         @OA\JsonContent(
     *             required={"guest_name", "room_id", "check_in_date", "check_out_date"},
     *             @OA\Property(property="guest_name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="room_id", type="integer", example=1),
     *             @OA\Property(property="check_in_date", type="string", format="date", example="2025-05-01"),
     *             @OA\Property(property="check_out_date", type="string", format="date", example="2025-05-03"),
     *             @OA\Property(property="promo_code", type="string", nullable=true, maxLength=50, example="SUMMER2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking created"),
     *             @OA\Property(
     *                 property="booking",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="guest_name", type="string", example="John Doe"),
     *                 @OA\Property(property="room_id", type="integer", example=1),
     *                 @OA\Property(property="check_in_date", type="string", format="date", example="2025-05-01"),
     *                 @OA\Property(property="check_out_date", type="string", format="date", example="2025-05-03"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="promo_code", type="string", example="SUMMER2025")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Room not available or already booked for the dates"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param  \App\Http\Requests\StoreBookingRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookingRequest $request)
    {
        // Check if room is available
        $room = Room::find($request->room_id);
        if (!$room->is_available) {
            return response()->json([
                'message' => 'Room is not available for booking'
            ], 409);
        }

        // Check for overlapping bookings
        $hasOverlap = Booking::where('room_id', $request->room_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('check_in_date', '<=', $request->check_in_date)
                            ->where('check_out_date', '>=', $request->check_out_date);
                    });
            })->exists();

        if ($hasOverlap) {
            return response()->json([
                'message' => 'Room is already booked for the selected dates'
            ], 409);
        }

        // Create booking using transaction to ensure data integrity
        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'guest_name' => $request->guest_name,
                'room_id' => $request->room_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'status' => 'pending',
                'promo_code' => $request->promo_code,
            ]);

            // Optional: Update room availability
            // $room->update(['is_available' => false]);

            DB::commit();

            return response()->json([
                'message' => 'Booking created',
                'booking' => $booking
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'An error occurred while creating the booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified booking.
     * 
     * @OA\Get(
     *     path="/api/bookings/{id}",
     *     operationId="getBookingById",
     *     tags={"Bookings"},
     *     summary="Get booking information",
     *     description="Returns booking data by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="booking",
     *                 type="object",
     *                 ref="#/components/schemas/Booking"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $booking = Booking::with('room')->find($id);
        
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }
        
        return response()->json([
            'booking' => $booking
        ]);
    }

    /**
     * Update the specified booking in storage.
     * 
     * @OA\Put(
     *     path="/api/bookings/{id}",
     *     operationId="updateBooking",
     *     tags={"Bookings"},
     *     summary="Update existing booking",
     *     description="Updates a booking by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Booking data to update",
     *         @OA\JsonContent(
     *             @OA\Property(property="guest_name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="check_in_date", type="string", format="date", example="2025-05-01"),
     *             @OA\Property(property="check_out_date", type="string", format="date", example="2025-05-03"),
     *             @OA\Property(property="status", type="string", enum={"pending", "confirmed", "cancelled"}, example="confirmed"),
     *             @OA\Property(property="promo_code", type="string", nullable=true, maxLength=50, example="SUMMER2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param  \App\Http\Requests\UpdateBookingRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }
        
        try {
            DB::beginTransaction();
            
            $booking->update($request->all());
            
            DB::commit();
            
            return response()->json([
                'message' => 'Booking updated',
                'booking' => $booking->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'An error occurred while updating the booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified booking from storage.
     * 
     * @OA\Delete(
     *     path="/api/bookings/{id}",
     *     operationId="deleteBooking",
     *     tags={"Bookings"},
     *     summary="Delete booking",
     *     description="Soft deletes a booking",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Booking not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }
        
        try {
            $booking->delete();
            
            return response()->json([
                'message' => 'Booking deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
