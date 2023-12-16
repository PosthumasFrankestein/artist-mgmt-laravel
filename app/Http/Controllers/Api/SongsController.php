<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;


class SongsController extends Controller
{

    public function save_song(Request $request)
    {
        // dd($request);
        // Validate api data
        $request->validate([
            "title" => "required",
            "album" => "required",
            "genre" => "required",
            "artist" => "required",
            "signature" => "required", // Corrected the typo in "required"
            "music_id" => "nullable|integer", // Use "nullable" and "integer" for optional numeric values
        ]);

        $post_data = [
            'id' => $request->music_id,
            "title" => $request->title,
            "album" => $request->album,
            "genre" => $request->genre, // Corrected the key name to "genres"
            "artist_id" => $request->artist,
            "updated_at" => now(),
        ];

        if ($request->signature == "update") {
            $affectedRows = DB::table("music")
                ->where("id", $request->music_id)
                ->update($post_data);

            if ($affectedRows > 0) {
                return response()->json([
                    'status' => true,
                    'message' => "Music Update Sucess.",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "No changes made or something went wrong, please try again later..",
                ]);
            }
        } else if ($request->signature == "insert") {
            unset($post_data['id']);
            $post_data['created_at'] = now();

            $insertedId = DB::table("music")->insertGetId($post_data);

            if ($insertedId) {
                return response()->json([
                    'status' => true,
                    'message' => "Music Insert Sucess.",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Something went wrong during insertion, please try again later..",
                ]);
            }
        }
    }

    public function fetch_all_song(Request $request)
    {
        $id = $request->id;

        // Get all songs for a specific artist
        $songs = DB::select("SELECT M.*, A.name as artist_name
                        FROM music M
                        JOIN artists A ON A.artist_id = M.artist_id
                        WHERE A.artist_id = ?", [$id]);

        return response()->json([
            'status' => true,
            'message' => "All songs data fetched",
            'data' => $songs,
        ]);
    }


    public function deleteSong(Request $request)
    {
        $id = $request->musicId;

        DB::delete("DELETE FROM music WHERE id = ?", [$id]);

        return response()->json([
            'status' => true,
            'message' => "Music Delated.",
        ]);
    }
}