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
        Schema::create('shippers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
        
            $table->string('name');
            $table->string('phone');
            $table->string('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
        
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippers');
    }
};
