<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="Hotel Booking API",
 *     version="1.0.0",
 *     description="API for managing hotel room bookings",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum API Token"
 * )
 * 
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="message", type="string", example="Error message")
 * )
 */
class SwaggerAnnotations 
{
    // This class doesn't need any implementation
    // It's only used for Swagger documentation annotations
}