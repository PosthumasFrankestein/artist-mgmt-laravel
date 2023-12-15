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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('lname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('date_of_birth')->nullable(); // Added date_of_birth column
            $table->string('phone')->nullable(); // Added phone column
            $table->string('gender')->nullable(); // Added gender column
            $table->string('address')->nullable(); // Added address column
            $table->string('role');
            $table->rememberToken();
            $table->timestamps();
        });

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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert 8 dummy records with role "artistmanager"
        for ($i = 1; $i <= 8; $i++) {
            DB::connection()->table('users')->insert([
                'fname' => $faker->firstName,
                'lname' => $faker->lastName,
                'email' => 'artistmanager' . $i . '@example.com',
                'password' => Hash::make('password'),
                'date_of_birth' => $faker->date,
                'phone' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'address' => $faker->address,
                'role' => 'artistmanager',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
