<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // типы пользователей
        \DB::table('user_type')->insert([
            [
                'id' => 1,
                'name' => 'Администратор',
                'code' => 'admin',
            ],
        ]);

        // пользователи
        \DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'admin',
                'type_id' => 1,
                'email' => 'test@gmail.com',
                'email_verified_at' => now(),
                'password' => 'test',
                'remember_token' => Str::random(10),
            ],

        ]);
    }
}
