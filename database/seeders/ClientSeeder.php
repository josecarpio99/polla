<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::create([
            'id_card' => '22222222',
            'name'    => 'Cliente 1'
        ]);

        Client::create([
            'id_card' => '33333333',
            'name'    => 'Cliente 2'
        ]);
    }
}
