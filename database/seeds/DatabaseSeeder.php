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
        //$this->call(UserSeeder::class);

        //$this->call(UsersTableSeeder::class); //2021-02-24 add, for sanctum
                                                //mark it, use command=> php artisan db:seed --class=UsersTableSeeder
    }
}
