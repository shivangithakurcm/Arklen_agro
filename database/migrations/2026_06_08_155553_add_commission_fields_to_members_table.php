<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionColumnsToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'direct_commission'))
                $table->decimal('direct_commission', 10, 2)->default(0);
            if (!Schema::hasColumn('members', 'level1_commission'))
                $table->decimal('level1_commission', 10, 2)->default(0);
            if (!Schema::hasColumn('members', 'level2_commission'))
                $table->decimal('level2_commission', 10, 2)->default(0);
            if (!Schema::hasColumn('members', 'balance'))
                $table->decimal('balance', 10, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['direct_commission', 'level1_commission', 'level2_commission', 'balance']);
        });
    }
}