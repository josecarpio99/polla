<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Play;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $play = Play::first();
        for ($i=0; $i < 5; $i++) {
            Ticket::create([
                'play_id'   => $play->id,
                'client_id' => Client::all()->random()->id,
                'user_id'   => User::all()->random()->id,
                'price'     => 2,
            ]);
        }
    }
}
