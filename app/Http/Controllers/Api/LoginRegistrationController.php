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
        ]);


        DB::insert('INSERT INTO users (fname, lname, email, password, date_of_birth, phone, gender, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, CAST(? AS CHAR), ?, ?, ?, ?)', [
            $request->fname,
            $request->lname,
            $request->email,
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
            $user = User::where('email', $request->email)->first();
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


    // Get all users' data API (GET)
    public function delete($request)
    {
        $idToDelete = $request->id;
        $tableName = $request->tableName;
        $email = $request->email;

        // DB::delete("DELETE FROM $tableName WHERE id = ?", [$idToDelete]);

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
            'success_records' => $allRecordsInsertedSuccessfully ? $successRecords : [],
            'failure_records' => $failureRecords,
        ];

        return response()->json($response);
    }
};
