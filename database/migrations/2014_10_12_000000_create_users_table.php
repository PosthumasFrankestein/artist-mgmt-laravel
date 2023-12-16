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
            $table->id()->autoIncrement();
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
            $table->string('man_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        DB::unprepared('
            CREATE TRIGGER users_after_insert
            AFTER INSERT ON users FOR EACH ROW
            BEGIN
                IF NEW.role = "artist" THEN
                    INSERT INTO artists (id, name, dob, phone, email, gender, address, first_release_year, no_of_albums_released, created_at, updated_at)
                    VALUES (NEW.man_id, CONCAT(NEW.fname, " ", NEW.lname), NEW.date_of_birth, NEW.phone, NEW.email, NEW.gender, NEW.address, NULL, NULL, NOW(), NOW());
                END IF;
            END;
        ');

        DB::unprepared('
        CREATE TRIGGER update_artists_info_after_user_update
        AFTER UPDATE ON users
        FOR EACH ROW
        BEGIN
            IF NEW.role = "artist" THEN
                UPDATE artists
                SET
                    name = CONCAT(NEW.fname, " ", NEW.lname),
                    dob = NEW.date_of_birth,
                    phone = NEW.phone,
                    email = NEW.email,
                    gender = NEW.gender,
                    address = NEW.address
                WHERE email = NEW.email;
            END IF;
        END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
