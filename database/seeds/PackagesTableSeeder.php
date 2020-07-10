<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packages')->insert([
            [
                'header' => 'ONE AND ONLY PAYMENT',
                'title' => 'Just Friend',
                'description' => 'Some quick example text to build on the card title and make up the bulk of the card\'s content.',
                'interval' => JUST_FRIEND_INTERVAL
            ],
            [
                'header' => 'MONTHLY SUBSCRIPTION',
                'title' => 'Best Friend',
                'description' => 'Some quick example text to build on the
                                  card title and make up the bulk of the card\'s content.',
                'interval' => BEST_FRIEND_INTERVAL

            ],
            [
                'header' => 'YEARLY MEMBERSHIP',
                'title' => 'Brotherhood',
                'description' => 'Some quick example text to build on the
                                  card title and make up the bulk of the card\'s content.',
                'interval' => BROTHERHOOD_INTERVAL

            ]
        ]);

    }
}
