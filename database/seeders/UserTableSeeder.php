<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Manager',
            'email'=>'user@manager.com',
            'password'=>Hash::make('123456'),
            'is_manager'=>true
        ]);

        User::create([
            'name'=>'Regular User1',
            'email'=>'user1@regular.com',
            'password'=>Hash::make('123456')
        ]);

        User::create([
            'name'=>'Regular User2',
            'email'=>'user2@regular.com',
            'password'=>Hash::make('123456')
        ]);
    }
}
