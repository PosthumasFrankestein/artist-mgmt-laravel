<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginRegistrationController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\SongsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login", [LoginRegistrationController::class, "login"]);
Route::post("register", [LoginRegistrationController::class, "register"]);
Route::post("save_song", [SongsController::class, "save_song"]);
Route::post("fetch_all_song", [SongsController::class, "fetch_all_song"]);



Route::group([
    "middleware" => ["auth:api"]
], function () {
    Route::get("profile", [LoginRegistrationController::class, "profile"]);
    Route::get("refresh", [LoginRegistrationController::class, "refreshToken"]);
    Route::get("logout", [LoginRegistrationController::class, "logout"]);
    Route::get("fetch_all_userdata", [LoginRegistrationController::class, "fetch_all_userdata"]);
    Route::get("fetchAllArtistsData", [ArtistController::class, "fetchAllArtistsData"]);

    Route::post("addartist", [LoginRegistrationController::class, "addartist"]);
    Route::put("bulkInsert", [ArtistController::class, "bulkInsert"]);
    Route::put("deleteUser", [LoginRegistrationController::class, "deleteUser"]);
    Route::put("deleteSong", [SongsController::class, "deleteSong"]);
    Route::put("updateUser", [LoginRegistrationController::class, "updateUser"]);
});
