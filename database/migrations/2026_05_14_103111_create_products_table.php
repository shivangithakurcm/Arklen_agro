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
   // create_products_table
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('product_code')->unique();
    $table->string('name');
    $table->string('category')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('bv', 10, 2)->default(0);   // Business Value
    $table->integer('stock')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
