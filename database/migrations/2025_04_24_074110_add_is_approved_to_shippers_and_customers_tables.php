<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->boolean('is_approved')->default(0);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_approved')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};

