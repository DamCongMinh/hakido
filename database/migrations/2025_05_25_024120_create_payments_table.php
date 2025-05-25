<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();  // Nếu là user
            $table->unsignedBigInteger('order_id');             // Mỗi payment gắn với 1 order
            
            $table->string('txn_ref')->unique();                // Mã giao dịch
            $table->bigInteger('amount');                       // Số tiền
            $table->string('bank_code')->nullable();            // Mã ngân hàng
            $table->string('status')->default('pending');       // Trạng thái
            $table->text('raw_data')->nullable();               // JSON từ VNPay
            
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
