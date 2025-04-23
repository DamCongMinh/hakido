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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('avata')->nullable()->change();
            $table->time('time_open')->nullable()->change();
            $table->time('time_close')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('avata')->nullable(false)->change();
            $table->time('time_open')->nullable(false)->change();
            $table->time('time_close')->nullable(false)->change();
        });
    }

};
