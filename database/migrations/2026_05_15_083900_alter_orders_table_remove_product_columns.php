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
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['order_product', 'order_quantity', 'order_value']);
        $table->decimal('total_value', 10, 2)->default(0)->after('city');
        $table->decimal('total_bv', 10, 2)->default(0)->after('total_value');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['total_value', 'total_bv']);
        $table->string('order_product')->after('member_id');
        $table->integer('order_quantity')->after('order_product');
        $table->decimal('order_value', 10, 2)->after('order_quantity');
    });
}
};
