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
        $superAdmin = User::create([
            'email' => 'superadmin@test.com',
            'role'  => 'superadmin',
            'name'  => 'superadmin',
            'password'  => Hash::make('12345678'),
        ]);

        $admin = User::create([
            'email' => 'admin@test.com',
            'role'  => 'admin',
            'name'  => 'admin',
            'password'  => Hash::make('12345678'),
        ]);

        $pos1 = User::create([
            'email' => 'pos@test.com',
            'role'  => 'pos',
            'name'  => 'pos',
            'password'  => Hash::make('12345678'),
        ]);

        $pos2 = User::create([
            'email' => 'pos2@test.com',
            'role'  => 'pos',
            'name'  => 'pos2',
            'password'  => Hash::make('12345678'),
        ]);

        $pos3 = User::create([
            'email' => 'pos3@test.com',
            'role'  => 'pos',
            'name'  => 'pos3',
            'password'  => Hash::make('12345678'),
        ]);

        $superAdmin->pos()->attach([$pos1->id, $pos2->id]);
    }
}
