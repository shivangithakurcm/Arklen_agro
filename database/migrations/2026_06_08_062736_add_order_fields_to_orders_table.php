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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('order_product')->nullable()->after('city');
        $table->integer('order_quantity')->default(0)->after('order_product');
        $table->decimal('order_value', 10, 2)->default(0)->after('order_quantity');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['order_product', 'order_quantity', 'order_value']);
    });
}
};
