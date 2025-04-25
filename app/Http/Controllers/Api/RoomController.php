<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Rules\PromoCodeRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     * 
     * @OA\Get(
     *     path="/api/rooms",
     *     operationId="getRoomsList",
     *     tags={"Rooms"},
     *     summary="Get list of rooms",
     *     description="Returns paginated list of rooms",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="rooms",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Room")),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="links", type="array", @OA\Items(
     *                     @OA\Property(property="url", type="string", nullable=true),
     *                     @OA\Property(property="label", type="string"),
     *                     @OA\Property(property="active", type="boolean")
     *                 )),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
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
        $rooms = Room::latest()
            ->paginate(10);

        return response()->json([
            'rooms' => $rooms
        ]);
    }

    /**
     * Display the specified room.
     * 
     * @OA\Get(
     *     path="/api/rooms/{id}",
     *     operationId="getRoomById",
     *     tags={"Rooms"},
     *     summary="Get room by ID",
     *     description="Returns a single room by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of room to return",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="room", ref="#/components/schemas/Room")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Store a newly created room.
     * 
     * @OA\Post(
     *     path="/api/rooms",
     *     operationId="storeRoom",
     *     tags={"Rooms"},
     *     summary="Create new room",
     *     description="Stores a new room and returns it",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Room data",
     *         @OA\JsonContent(
     *             required={"number", "type", "is_available"},
     *             @OA\Property(property="number", type="string", example="101", description="Room number"),
     *             @OA\Property(property="type", type="string", example="deluxe", description="Room type"),
     *             @OA\Property(property="is_available", type="boolean", example=true, description="Room availability status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room created successfully"),
     *             @OA\Property(property="room", ref="#/components/schemas/Room")
     *         )
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Update the specified room.
     * 
     * @OA\Put(
     *     path="/api/rooms/{id}",
     *     operationId="updateRoom",
     *     tags={"Rooms"},
     *     summary="Update an existing room",
     *     description="Updates a room and returns it",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of room to update",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Room data",
     *         @OA\JsonContent(
     *             @OA\Property(property="number", type="string", example="102", description="Room number"),
     *             @OA\Property(property="type", type="string", example="standard", description="Room type"),
     *             @OA\Property(property="is_available", type="boolean", example=false, description="Room availability status")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room updated successfully"),
     *             @OA\Property(property="room", ref="#/components/schemas/Room")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Remove the specified room.
     * 
     * @OA\Delete(
     *     path="/api/rooms/{id}",
     *     operationId="deleteRoom",
     *     tags={"Rooms"},
     *     summary="Delete a room",
     *     description="Deletes a room",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of room to delete",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
}