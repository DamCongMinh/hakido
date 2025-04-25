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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id')->nullable(); // chưa khóa
            }
        });

        Schema::table('shippers', function (Blueprint $table) {
            if (!Schema::hasColumn('shippers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id')->nullable(); // chưa khóa
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });

        Schema::table('shippers', function (Blueprint $table) {
            if (Schema::hasColumn('shippers', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
