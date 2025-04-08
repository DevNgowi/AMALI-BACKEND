<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Gender;
use App\Models\Position;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function indexCity() {
        $cities = City::select('id', 'name')->get();
        return response()->json([
            'data' => $cities,
            'code' => 200,
            'message' => 'success',
        ]);
    }
    

    public function indexPositions() {
        $positions = Position::select('id', 'title as name')->get();
        return response()->json([
            'data' => $positions,
            'code' => 200,
            'message' => 'success',
        ]);
    }

    public function indexGenders() {
        $positions = Gender::select('id', 'name')->get();
        return response()->json([
            'data' => $positions,
            'message' => 'success',
        ], 200);
    }


    
}
