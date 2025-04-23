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
        Schema::table('shippers', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->string('avata')->nullable()->change();
            $table->string('area')->nullable()->change();    
        });
    }

    public function down(): void
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->string('avata')->nullable(false)->change();
            $table->string('area')->nullable(false)->change();
        });
    }
};
