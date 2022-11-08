<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'superadmin@test.com',
            'role'  => 'superadmin',
            'name'  => 'superadmin',
            'password'  => Hash::make('12345678'),
        ]);

        User::create([
            'email' => 'admin@test.com',
            'role'  => 'admin',
            'name'  => 'admin',
            'password'  => Hash::make('12345678'),
        ]);

        User::create([
            'email' => 'pos@test.com',
            'role'  => 'pos',
            'name'  => 'pos',
            'password'  => Hash::make('12345678'),
        ]);
    }
}
