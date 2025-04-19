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
        // TABLE: foods
        if (!Schema::hasColumn('foods', 'restaurant_id')) {
            Schema::table('foods', function (Blueprint $table) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
                $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            });
        }

        // TABLE: beverages
        if (!Schema::hasColumn('beverages', 'restaurant_id')) {
            Schema::table('beverages', function (Blueprint $table) {
                $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
                $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });

        Schema::table('beverages', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });
    }
};
