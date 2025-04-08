<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private const LOGIN_VALIDATION_RULES = [
        'username' => 'required|string|max:255',
        'password' => 'required|string|min:8',
    ];

    private const LOGIN_VALIDATION_MESSAGES = [
        'username.required' => 'The username field is mandatory.',
        'username.string' => 'The username must be a string.',
        'username.max' => 'The username must not exceed 255 characters.',
        'password.required' => 'The password field is mandatory.',
        'password.string' => 'The password must be a string.',
        'password.min' => 'The password must be at least 8 characters long.',
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), self::LOGIN_VALIDATION_RULES, self::LOGIN_VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['error' => 'username or password wrong'], 401);
        }

        return $this->generateLoginResponse(Auth::user());
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function logout(Request $request)
    // {
    //     return $request->all();
    //     Auth::logout();

    //     // Invalidate the session
    //     $request->session()->invalidate();

    //     // Regenerate the CSRF token for security
    //     $request->session()->regenerateToken();

    //     // Redirect to login page
    //     return redirect()->route('login')->with('message', 'Successfully logged out');
    // }


    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function generateLoginResponse(User $user): \Illuminate\Http\JsonResponse
    {
        $data = [
            'token' => $user->createToken('pos')->plainTextToken,
            'user' => $this->formatUser($user),
        ];

        return response()->json(['message' => 'Login successful', 'data' => $data], 200);
    }

    /**
     * @param User $user
     * @return array
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'phone_verified' => $user->phone_verified_at !== null,
            'position' => $user->position,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->permissions->pluck('name'),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
