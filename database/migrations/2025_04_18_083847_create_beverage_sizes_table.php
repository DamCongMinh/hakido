<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beverage_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beverage_id')->constrained('beverages')->onDelete('cascade');
            $table->string('size'); // S, M, L...
            $table->decimal('price', 10, 2); // Giá theo size
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beverage_sizes');
    }
};
