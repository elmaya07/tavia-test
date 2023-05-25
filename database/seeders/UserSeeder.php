<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        $user = new User();
        $user->nama = $faker->name();
        $user->email = $faker->email();
        $user->no_telp = $faker->phonenumber();
        $user->password = Hash::make('password');
        $user->foto = 'user.jpg';
        $user->save();
    }
}
