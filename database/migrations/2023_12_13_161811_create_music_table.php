<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreateMusicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('music', function (Blueprint $table) {
            $table->id(); // Auto-incremental primary key
            $table->unsignedBigInteger('artist_id');
            $table->foreign('artist_id')->references('artist_id')->on('artists')->onDelete('cascade');
            $table->string('title');
            $table->string('album');
            $table->string('genre');
            $table->timestamps(); // Adds created_at and updated_at columns
        });

        DB::unprepared('
            CREATE TRIGGER update_artists_info_after_insert
            AFTER INSERT ON music
            FOR EACH ROW
            BEGIN
                UPDATE artists
                SET
                    first_release_year = IFNULL((SELECT MIN(YEAR(created_at)) FROM music WHERE artist_id = NEW.artist_id), NULL),
                    no_of_albums_released = (SELECT COUNT(*) FROM music WHERE artist_id = NEW.artist_id)
                WHERE artist_id = NEW.artist_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_artists_info_after_update
            AFTER UPDATE ON music
            FOR EACH ROW
            BEGIN
                UPDATE artists
                SET
                    first_release_year = IFNULL((SELECT MIN(YEAR(created_at)) FROM music WHERE artist_id = NEW.artist_id), NULL),
                    no_of_albums_released = (SELECT COUNT(*) FROM music WHERE artist_id = NEW.artist_id)
                WHERE artist_id = NEW.artist_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('music');
    }
}
