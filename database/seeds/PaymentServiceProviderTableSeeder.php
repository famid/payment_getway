<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentServiceProviderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_service_providers')->insert([
            [
                'name' => MOLLIE
            ],
            [
                'name' => STRIPE
            ]
        ]);
    }
}
