<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Test1 User',
                'email' => 'test1@email.com',
                'password' => \Illuminate\Support\Facades\Hash::make('1234')
            ],
            [
                'name' => 'Test2 User',
                'email' => 'test2@email.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345')
            ]

        ]);
    }
}
