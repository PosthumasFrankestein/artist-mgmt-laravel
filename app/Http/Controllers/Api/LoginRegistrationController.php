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

        DB::insert('INSERT INTO users (fname, lname, email,role, password, date_of_birth, phone, gender, address, created_at, updated_at,man_id) VALUES (?, ?,?, ?, ?, ?, CAST(? AS CHAR), ?, ?, ?, ?,case when "' . $request->role . '"="artist" then ' . $request->man_id . ' else null end )', [
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
            $user = DB::selectOne('SELECT U.*, A.artist_id FROM users U LEFT JOIN artists A ON A.email = U.email WHERE U.email = ?', [$request->email]);

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
};
