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
        // Thêm cột vào bảng foods
        Schema::table('foods', function (Blueprint $table) {
            $table->decimal('old_price', 10, 2)->nullable()->after('id');
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('old_price');
        });

        // Thêm cột vào bảng beverage_sizes
        Schema::table('beverage_sizes', function (Blueprint $table) {
            $table->decimal('old_price', 10, 2)->nullable()->after('id');
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('old_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa cột nếu rollback
        Schema::table('foods', function (Blueprint $table) {
            $table->dropColumn(['old_price', 'discount_percent']);
        });

        Schema::table('beverage_sizes', function (Blueprint $table) {
            $table->dropColumn(['old_price', 'discount_percent']);
        });
    }
};
