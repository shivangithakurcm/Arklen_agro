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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_no')->unique();
        $table->foreignId('member_id')->constrained()->onDelete('cascade');
        $table->string('order_product');
        $table->integer('order_quantity');
        $table->date('order_date');
        $table->decimal('order_value', 10, 2);
        $table->string('city');
        $table->enum('status', ['pending', 'processing', 'delivered', 'cancelled'])->default('pending');
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
