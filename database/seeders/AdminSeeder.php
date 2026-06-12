<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Admin User
        User::updateOrCreate(
            ['seller_id' => 'ADMIN001'],
            [
                'name'      => 'Admin',
                'seller_id' => 'ADMIN001',
                'password'  => Hash::make('admin1234'),
            ]
        );

        // ✅ Admin Member
        Member::updateOrCreate(
            ['seller_id' => 'ADMIN001'],
            [
                'seller_id'       => 'ADMIN001',
                'first_name'      => 'Admin',
                'last_name'       => 'User',
                'contact'         => '0000000000',
                'password'        => Hash::make('admin1234'),
                'date_of_joining' => now(),
                'is_active'       => 1,
                'position'        => 'left',
            ]
        );
    }
}