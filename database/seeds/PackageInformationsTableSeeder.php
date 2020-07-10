<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageInformationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('package_information')->insert([
            [
                'package_id' => 1,
                'amount' => JUST_FRIEND_AMOUNT,
                'currency' => EUR

            ],
            [
                'package_id' => 1,
                'amount' => JUST_FRIEND_AMOUNT,
                'currency' => USD

            ],
            [
                'package_id' => 2,
                'amount' => BEST_FRIEND_AMOUNT,
                'currency' => EUR

            ],
            [
                'package_id' => 2,
                'amount' => BEST_FRIEND_AMOUNT,
                'currency' => USD

            ],
            [
                'package_id' => 3,
                'amount' => BROTHERHOOD_AMOUNT,
                'currency' => EUR
            ],
            [
                'package_id' => 3,
                'amount' => BROTHERHOOD_AMOUNT,
                'currency' => USD
           ]
        ]);
    }
}
