<?php

use Illuminate\Database\Seeder;

class countingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countings')->insert([
            'id' => Str::random(10),
            'id_camera' => rand(1,2),
            'lane' => rand(1,2),
            'type' => rand(1,4),
            'created_at' => date('Y-m-d 15:i:s'),
        ]);
    }
}
