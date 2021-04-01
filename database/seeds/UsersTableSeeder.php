<?php

use Illuminate\Database\Seeder;

use App\User;                   //2021-02-24 add, for sanctum

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(User::class, 100)->create(); //2021-02-24 add, for sanctum
    }
}
