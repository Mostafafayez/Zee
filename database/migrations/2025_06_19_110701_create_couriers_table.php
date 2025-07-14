<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('couriers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('national_id');
        $table->string('vehicle_type');
        $table->decimal('rating', 3, 2)->default(0);
        $table->string('license_number')->nullable();
        $table->string('vehicle_plate_number')->nullable();
        $table->string('license_image')->nullable();
        $table->string('vehicle_plate_image')->nullable();
            $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
