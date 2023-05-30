<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          
        DB::table('rewards')->insert(
            [
                'id' => 1,
                'name' => 'Daily rewards',
                'life_span' => 168
            ]
        );
    }
}