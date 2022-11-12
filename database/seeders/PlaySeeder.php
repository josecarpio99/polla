<?php

namespace Database\Seeders;

use App\Models\Play;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prize = [
            [
                'position'   => 1,
                'percentage' => 50,
            ],
            [
                'position'   => 2,
                'percentage' => 25,
            ]
        ];

        Play::create([
            'race_track_id' => 1,
            'start_at'      => Carbon::today(),
            'close_at'        => Carbon::today()->addHours(4),
            'prize'         => json_encode($prize)
        ]);
    }
}
