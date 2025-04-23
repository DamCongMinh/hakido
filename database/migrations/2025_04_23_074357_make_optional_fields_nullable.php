<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
        });

        Schema::table('shippers', function (Blueprint $table) {
            $table->string('area')->nullable()->change();
            $table->string('avata')->nullable()->change();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('address')->nullable()->change();
            $table->string('avata')->nullable()->change();
            $table->time('time_open')->nullable()->change();
            $table->time('time_close')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Optional: rollback logic (không bắt buộc)
    }
};

