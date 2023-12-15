<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;


class LoginRegistrationController extends Controller
{
    //Registation API (POST, formdata)
    public function register(Request $request)
    {

        // Validation
        $request->validate([
            "fname" => "required",
            "lname" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
            "date_of_birth" => "required|date", // Updated to make date_of_birth required
            "phone" => "required|integer", // Updated to make phone required
            "gender" => "required|string", // Updated to make gender required
            "address" => "required|string", // Updated to make address required
            "role" => "required|string",
        ]);


        DB::insert('INSERT INTO users (fname, lname, email,role, password, date_of_birth, phone, gender, address, created_at, updated_at) VALUES (?, ?,?, ?, ?, ?, CAST(? AS CHAR), ?, ?, ?, ?)', [
            $request->fname,
            $request->lname,
            $request->email,
            $request->role,
            Hash::make($request->password),
            $request->date_of_birth,
            $request->phone,
            $request->gender,
            $request->address,
            now(),
            now(),
        ]);


        return response()->json([
            "status" => true,
            "message" => "New User Created Successfully",
        ]);
    }

    public function updateUser(Request $request)
    {
        // Validation
        $user = DB::table('users')
            ->where('id', $request->id)
            ->first();


        if ($user) {
            DB::table('users')
                ->where('id', $request->id)
                ->update([
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'date_of_birth' => $request->date_of_birth,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'updated_at' => now(),
                ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "User Doesn't exists.",
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => "User updated Successfully",
        ]);
    }

    //Add New Artist API (POST, formdata)
    public function addartist(Request $request)
    {

        // Validation
        $request->validate([
            "fname" => "required",
            "lname" => "required",
            "id" => "required",
            "email" => "required|email",
            "date_of_birth" => "required|date", // Updated to make date_of_birth required
            "phone" => "required|integer", // Updated to make phone required
            "gender" => "required|string", // Updated to make gender required
            "address" => "required|string", // Updated to make address required
            "first_release_year" => "required|integer",
            "no_of_albums_released" => "required|integer",
        ]);


        $artistData = [
            'name' => $request->fname,
            'dob' => $request->date_of_birth,
            "email" => $request->email,
            "phone" => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'first_release_year' => $request->first_release_year,
            'no_of_albums_released' => $request->no_of_albums_released,
            'id' => $request->id,
        ];

        // Raw insert query for the artists table
        DB::insert('INSERT INTO artists (id,email, name, phone,dob, gender, address, first_release_year, no_of_albums_released, created_at, updated_at) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?,?)', [
            $artistData['id'],
            $artistData['email'],
            $artistData['name'],
            $artistData['phone'],
            $artistData['dob'],
            $artistData['gender'],
            $artistData['address'],
            $artistData['first_release_year'],
            $artistData['no_of_albums_released'],
            now(),
            now(),
        ]);


        return response()->json([
            "status" => true,
            "message" => "New User Created Successfully",
        ]);
    }

    // Login API (POST, formdata)
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // JWT auth and attempt
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password,
        ]);

        // Response
        if (!empty($token)) {
            // $qry = 'select * from users where email=?'.$request->email;
            // $user = User::where('email', $request->email)->first();
            $user = DB::selectOne('SELECT U.*, A.artist_id FROM users U LEFT JOIN artists A ON A.email = U.email WHERE U.email = ?', [$request->email]);

            // $user = DB::select('SELECT U.*,A.artist_id FROM users U left join artists A on A.email=U.email where A.email=?', $request->email);

            return response()->json([
                "status" => true,
                "message" => "User logged in successfully.",
                "token" => $token,
                "userdata" => $user
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid login details."
        ]);
    }

    // Profile API (GET)
    public function profile()
    {

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "user" => $userdata,
        ]);
    }

    // Refresh token API (GET) 
    public function refreshToken()
    {

        // $newToken = auth()->refresh();
        $newToken = "123";

        return response()->json([
            "status" => true,
            "message" => "New token generated.",
            "token" => $newToken,
        ]);
    }

    // Logout API (GET)
    public function logout()
    {

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "User loggod out successfully",
        ]);
    }

    // Get all users' data API (GET)
    public function fetch_all_userdata()
    {
        // Get all users from Users table
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => "All users's data fetched",
            'data' => $users,
        ]);
    }

    public function fetchAllArtistsData()
    {
        // Use the DB facade to perform a raw query
        $artists = DB::select('SELECT * FROM artists');

        return response()->json([
            'status' => true,
            'message' => "All artists' data fetched",
            'data' => $artists,
        ]);
    }



    // Get all users' data API (GET)
    public function deleteUser(Request $request)
    {
        $email = $request->email;
        $tablename = $request->tablename;

        DB::delete("DELETE FROM $tablename WHERE email = ?", [$email]);

        return response()->json([
            'status' => true,
            'message' => "$email Delated.",
        ]);
    }


    public function bulkInsert(Request $request)
    {
        $selectedRows = $request->input('selectedRows');
        $successRecords = [];
        $failureRecords = [];

        foreach ($selectedRows as $row) {
            try {
                // Assuming each item in $selectedRows is an associative array representing a row
                // Adjust the column names as needed
                $insertedId = DB::table('artists')->insertGetId($row);

                if ($insertedId) {
                    // Insertion successful
                    $successRecords[] = $row;
                } else {
                    // Insertion failed
                    $failureRecords[] = [
                        'record' => $row,
                        'error' => 'Failed to insert the record.',
                    ];
                }
            } catch (\Exception $e) {
                $failureRecords[] = [
                    'record' => $row,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $allRecordsInsertedSuccessfully = empty($failureRecords);

        $response = [
            'status' => $allRecordsInsertedSuccessfully,
            'message' => $allRecordsInsertedSuccessfully ? 'All records inserted successfully.' : 'Some records failed to insert.',
            'success_records' => $successRecords,
            'failure_records' => $failureRecords,
        ];

        return response()->json($response);
    }

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
};
