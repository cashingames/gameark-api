<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('plans')->insert(
            [
                'name' => 'Free Plan',
                'description' => 'Play a maximun of 10 games',
                'price' => 0.00,
                'game_count' => 10,
                'background_color'=> '#FAC502'
            ]
        );

        DB::table('plans')->insert(
            [
                'name' => 'Double O Plan',
                'description' => 'Play a maximum of 15 games',
                'price' => 500.00,
                'game_count' => 15,
                'background_color'=>'#A35EBB'
            ]
        );

        DB::table('plans')->insert(
            [
                'name' => 'Dicey Multiples Plan',
                'description' => 'Play a maximum of 20 games',
                'price' => 800.00,
                'game_count' => 20,
                'background_color'=> '#2D9CDB'
            
            ]
        );

        DB::table('plans')->insert(
            [
                'name' => 'The Ultimate Plan',
                'description' => 'Play a maximum of 25 games',
                'price' => 1000.00,
                'game_count' => 25,
                'background_color'=>'#EF2F55'
            ]
        );

    }
}