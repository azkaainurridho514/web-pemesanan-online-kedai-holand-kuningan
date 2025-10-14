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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('order_code')->unique(); 
            $table->string('name'); 
            $table->string('phone'); 
            $table->string('table_number'); 
            $table->string('total_price'); 
            $table->enum('payment_method', ['cash', 'transfer'])->default('transfer'); 
            $table->enum('status', ['menunggu', 'diproses', 'dihidangkan', 'selesai', 'batal'])->default('menunggu');
            $table->timestamp('completed_at')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
