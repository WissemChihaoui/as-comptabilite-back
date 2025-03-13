<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Update the demenagement, adresse, and situation fields for the current user.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the input data
        $request->validate([
            'demenagement' => 'nullable|date',
            'adresse' => 'nullable|string|max:255',
            'situation' => 'nullable|string|max:255',
        ]);

        $date = Carbon::parse($request -> date); // Example date
        $formattedDate = $date->format('Y-m-d'); // Converts to YYYY-MM-DD

        // Update user data
        $user->update([
            'demenagement' => $formattedDate,
            'adresse' => $request->adresse,
            'situation' => $request->situation,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user,
        ], 200);
    }
    
    public function updateProfileMatricule(Request $request)
    {
        $user = Auth::user();

        // Validate the input data
        $request->validate([
            'matricule' => 'nullable|string|max:255',
        ]);

        // Update user data
        $user->update([
            'matricule' => $request->matricule,
        ]);

        return response()->json([
            'message' => 'Matricule updated successfully!',
            'user' => $user,
        ], 200);
    }
}
