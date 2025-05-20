<?php
namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users|unique:email_verifications,email',
            'password' => 'required|string|min:6',
        ]);

        $token = Str::random(64);

        DB::table('email_verifications')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'token'      => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verificationLink = url("/api/verify-email/{$token}");

        Mail::to($request->email)->send(new VerifyEmail($verificationLink));

        return response()->json([
            'message' => 'Un lien de vérification a été envoyé à votre adresse e-mail.',
        ]);
    }

    // User Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'accessToken' => $token]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Get Authenticated User
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function verifyEmail($token)
    {
        $record   = DB::table('email_verifications')->where('token', $token)->first();
        $frontend = config('app.frontend_url', 'http://localhost:3031');
        if (! $record || now()->diffInHours($record->created_at) > 24) {
            if ($record) {
                DB::table('email_verifications')->where('token', $token)->delete();
            }

            return redirect()->away("{$frontend}/auth/jwt/sign-in?error=invalid-or-expired-link");
        }

        if (User::where('email', $record->email)->exists()) {
            return redirect()->away("{$frontend}/auth/jwt/sign-in?error=already-verified");
        }

        $user = User::create([
            'name'     => $record->name,
            'email'    => $record->email,
            'password' => $record->password,
        ]);

        DB::table('email_verifications')->where('token', $token)->delete();

        // ✅ Redirect to login with success message
        return redirect()->away("{$frontend}/auth/jwt/sign-in?success=email-verified");
    }

}
