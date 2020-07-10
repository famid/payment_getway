<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MandatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mandates')->insert([
            [
                'user_id' =>1,
                'user_account'=>'NL55INGB0000000000',
                'user_bic'=>'INGBNL2A',
                'signature_date'=>'2020-06-04',
                'mandate_reference' => 'Test YOUR-COMPANY-MD13804'
            ],
            [
                'user_id' =>2,
                'user_account'=>'NL56INGB0000000000',
                'user_bic'=>'INGBNL2B',
                'signature_date'=>'2020-06-05',
                'mandate_reference' => 'Test YOUR-COMPANY-MD13904'
            ]
        ]);
    }
}
