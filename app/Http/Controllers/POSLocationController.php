<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;

class POSLocationController extends Controller
{
    //
=======
use App\Models\PosLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class POSLocationController extends Controller
{
    public function storePOSLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20', // Example validation for phone, adjust as needed
            'email' => 'nullable|email|max:255', // Example validation for email, adjust as needed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);                                    
        }

        try {
            $posLocation = new PosLocation(); // Assuming your model is PosLocation and in App\Models namespace
            $posLocation->name = $request->name;
            $posLocation->address = $request->address;
            $posLocation->contact_phone = $request->contact_phone;
            $posLocation->email = $request->email;
            $posLocation->save();

            return redirect()->back()->with('message', 'Successfully create new Pos Location');
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('POS Location Creation Failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create POS Location. Please try again.', // Generic error message for user
            ], 500); // 500 Internal Server Error for general exceptions
        }
    }
>>>>>>> main
}
