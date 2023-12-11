<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**p
     * Run the migrations.
     */
    public function up(): void
    {
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
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
