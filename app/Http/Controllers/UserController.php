<?php
namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the input data
        $request->validate([
            'adresse'   => 'nullable|string|max:255',
            'matricule' => 'nullable|string|max:255',
        ]);

        $date          = Carbon::parse($request->date); // Example date
        $formattedDate = $date->format('Y-m-d');        // Converts to YYYY-MM-DD

        // Update user data
        $user->update([
            'adresse'   => $request->adresse,
            'matricule' => $request->matricule,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user'    => $user,
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
            'user'    => $user,
        ], 200);
    }

    public function getUsers()
    {
        $users = User::where('isAdmin', 0)->get();

        return response()->json($users);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $id,
            'matricule'    => 'nullable|string|max:255',
            'adresse'      => 'nullable|string|max:255',
        ]);

        // Format demenagement if provided
        // if ($request->filled('demenagement')) {
        //     $date                      = Carbon::parse($request->demenagement);
        //     $validated['demenagement'] = $date->format('Y-m-d');
        // }

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user'    => $user,
        ]);
    }
}
