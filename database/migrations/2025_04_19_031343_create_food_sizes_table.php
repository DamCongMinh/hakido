<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodSizesTable extends Migration
{
    public function up()
    {
        Schema::create('food_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id'); // liên kết với bảng foods
            $table->string('size'); // ví dụ: Small, Medium, Large
            $table->decimal('price', 8, 2); // giá tương ứng với size
            $table->timestamps();

            // Foreign key (nếu bạn có bảng foods)
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('food_sizes');
    }
}
