<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UserSeeder::class);
         $this->call(MandatesTableSeeder::class);
         $this->call(PackagesTableSeeder::class);
         $this->call(PackageInformationsTableSeeder::class);
         $this->call(PaymentServiceProviderTableSeeder::class);
    }
}
