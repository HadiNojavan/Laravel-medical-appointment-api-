<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->safe()->all();

        $user=User::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'password'=>Hash::make($validated['password']),
            'role'=>UserRole::Patient
        ]);

        $token=$user->createToken('authToken',['*'],\Illuminate\Support\now()->addMinutes(500))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user),
            'message' => 'Patient created successfully',
        ], 201);

    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('authToken', ['*'], now()->addMinutes(500))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user),
            'message' => 'Login successful',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user information'=>new UserResource($request->user())]);
    }
}
