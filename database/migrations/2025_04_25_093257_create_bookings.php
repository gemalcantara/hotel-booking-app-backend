<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Fields: id , guest_name (string), room_id (foreign key), check_in_date (date), check_out_date (date), status (enum: pending , confirmed , cancelled ), promo_code (string, nullable), created_at , updated_at , deleted_at (soft deletes). 
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled']);
            $table->string('promo_code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
