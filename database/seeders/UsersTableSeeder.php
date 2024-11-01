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
            'username'  => 'superadmin',
            'email'     => 'superadmin@test.com',
            'role'      => 'superadmin',
            'name'      => 'superadmin',
            'password'  => Hash::make('12345678'),
        ]);

        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@test.com',
            'role'  => 'admin',
            'name'  => 'admin',
            'password'  => Hash::make('12345678'),
        ]);

        $pos1 = User::create([
            'username' => 'taquilla1',
            'email' => 'taquilla1@test.com',
            'role'  => 'pos',
            'name'  => 'taquilla 1',
            'password'  => Hash::make('12345678'),
        ]);

        $pos2 = User::create([
            'username' => 'taquilla2',
            'email' => 'taquilla2@test.com',
            'role'  => 'pos',
            'name'  => 'taquilla 2',
            'password'  => Hash::make('12345678'),
        ]);

        $pos3 = User::create([
            'username' => 'taquilla3',
            'email' => 'taquilla3@test.com',
            'role'  => 'pos',
            'name'  => 'taquilla 3',
            'password'  => Hash::make('12345678'),
        ]);

        $admin->pos()->attach([$pos1->id, $pos2->id]);
    }
}
