<?php

namespace Database\Seeders;

use App\Models\RaceTrack;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RaceTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RaceTrack::create(['name' => 'La Rinconada']);
        RaceTrack::create(['name' => 'Gulfstream Park']);
    }
}
