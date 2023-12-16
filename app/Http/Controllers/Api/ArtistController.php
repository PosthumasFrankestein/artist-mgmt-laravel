<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;


class ArtistController extends Controller
{

    public function fetchAllArtistsData()
    {
        // Use the DB facade to perform a raw query
        $artists = DB::select('SELECT U.*,A.first_release_year,A.no_of_albums_released,A.id as manager_id,A.artist_id FROM users U join artists A on A.email=U.email where role="artist"');

        return response()->json([
            'status' => true,
            'message' => "All artists' data fetched",
            'data' => $artists,
        ]);
    }

    public function bulkInsert(Request $request)
    {
        $selectedRows = $request->input('selectedRows');
        $manager_id = $request->input('manager_id');
        $created_at = now();
        $updated_at = now();

        $successRecords = [];
        $failureRecords = [];

        foreach ($selectedRows as $row) {
            try {
                $post_data['man_id'] = $manager_id;
                $post_data['created_at'] = $created_at;
                $post_data['updated_at'] = $updated_at;
                foreach ($row as $key => $value) {
                    $post_data[$key] = $value;
                }

                if (array_key_exists('password', $post_data)) {
                    $post_data['password'] = bcrypt($post_data['password']);
                }

                $insertedId = DB::table('users')->insertGetId($post_data);

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

}