<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Booking",
 *     required={"guest_name", "room_id", "check_in_date", "check_out_date", "status"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="guest_name", type="string", maxLength=255, example="John Doe"),
 *     @OA\Property(property="room_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="check_in_date", type="string", format="date", example="2025-05-01"),
 *     @OA\Property(property="check_out_date", type="string", format="date", example="2025-05-03"),
 *     @OA\Property(property="status", type="string", enum={"pending", "confirmed", "cancelled"}, example="pending"),
 *     @OA\Property(property="promo_code", type="string", nullable=true, maxLength=50, example="SUMMER2025"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'guest_name',
        'room_id',
        'check_in_date',
        'check_out_date',
        'status',
        'promo_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    /**
     * Get the room associated with the booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
