<?php

namespace App\Http\Controllers;

use App\Models\TargetValue;
use Illuminate\Http\Request;

class TargetValueController extends Controller
{
    public static function findTrack(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }
        $tracks = TargetValue::findTrack($term);
        $formatted_tracks = [];

        foreach ($tracks as $track) {
            $formatted_tracks[] = ['id' => $track->track, 'text' => $track->track];
        }
        return response()->json($formatted_tracks);
    }
}
