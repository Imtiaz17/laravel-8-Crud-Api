<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
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
        DB::table('users')->insert([
            'name'=>'Jhon Doe',
            'email'=>'admin@gmail.com',
            'password'=> bcrypt('admin12') ,
            'email_verified_at'=> now() ,
            'created_at'=> now(),
            'updated_at'=> now() ,
        ]);
    }
}
