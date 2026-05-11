<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id')->unique();
            $table->string('sponsor_id')->nullable();
            $table->enum('position', ['left', 'right'])->default('left');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact', 15);
            $table->string('address');
            $table->string('aadhar_no', 12)->nullable();
            $table->string('profile_image')->nullable();
            $table->date('date_of_joining');
            $table->string('password');
            $table->decimal('bv_left', 10, 2)->default(0);
            $table->decimal('bv_right', 10, 2)->default(0);
            $table->decimal('sponsor_income', 10, 2)->default(0);
            $table->decimal('direct_sponsor_income', 10, 2)->default(0);
            $table->decimal('team_income', 10, 2)->default(0);
            $table->decimal('pay_income', 10, 2)->default(0);
            $table->decimal('total_income', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('team_bv', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};