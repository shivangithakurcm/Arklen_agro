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
    Schema::table('products', function (Blueprint $table) {
        $table->decimal('direct_commission', 5, 2)->default(0)->after('product_price');
        $table->decimal('new_joinee', 5, 2)->default(0)->after('direct_commission');
        $table->decimal('level_1', 5, 2)->default(0)->after('new_joinee');
        $table->decimal('level_2', 5, 2)->default(0)->after('level_1');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['direct_commission', 'new_joinee', 'level_1', 'level_2']);
    });
}
};
