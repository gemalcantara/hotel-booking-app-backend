<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Room",
 *     required={"number", "type", "is_available"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="number", type="string", example="101"),
 *     @OA\Property(property="type", type="string", example="deluxe"),
 *     @OA\Property(property="is_available", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'type',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
