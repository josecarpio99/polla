<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'name'  => 'play_cost',
            'value' => '2'
        ]);

        Setting::create([
            'name'  => 'system_percentage',
            'value' => '10'
        ]);
    }
}
