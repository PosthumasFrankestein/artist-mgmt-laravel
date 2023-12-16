<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $faker = Faker::create();

        DB::table('users')->insert([
            'fname' => 'Admin',
            'lname' => 'User',
            'email' => 'a@gmail.com',
            'password' => Hash::make('12'),
            'date_of_birth' => '1990-01-01', // Replace with an actual date
            'phone' => '1234567890', // Replace with an actual phone number
            'gender' => 'Male', // Replace with an actual gender
            'address' => '123 Admin Street, City',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'fname' => 'Artist Manager',
            'lname' => 'User',
            'email' => 'b@gmail.com',
            'password' => Hash::make('12'),
            'date_of_birth' => '1990-01-01', // Replace with an actual date
            'phone' => '1234567899', // Replace with an actual phone number
            'gender' => 'Male', // Replace with an actual gender
            'address' => '123 Admin Street, City',
            'role' => 'artistmanager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'fname' => 'Artist',
            'lname' => 'User',
            'email' => 'c@gmail.com',
            'password' => Hash::make('12'),
            'date_of_birth' => '1990-01-01', // Replace with an actual date
            'phone' => '1234567899', // Replace with an actual phone number
            'gender' => 'Male', // Replace with an actual gender
            'address' => '123 Admin Street, City',
            'role' => 'artist',
            'man_id' => '2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
