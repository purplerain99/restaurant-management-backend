<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        $user = User::where(
            'email',
            $credentials['email']
        )->first();

        if (
            ! $user ||
            ! Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Email သို့မဟုတ် Password မှားယွင်းနေပါသည်။',
                ],
            ]);
        }

        /*
         * Login တိုင်း token အသစ်ထုတ်မယ်။
         *
         * အရင် token အားလုံးကိုဖျက်ချင်ရင်
         * $user->tokens()->delete();
         * ကို createToken မတိုင်ခင်ထည့်နိုင်ပါတယ်။
         */
        $token = $user->createToken(
            'restaurant-management'
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login အောင်မြင်ပါသည်။',

            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout အောင်မြင်ပါသည်။',
        ]);
    }

    /**
     * Current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,

            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }
}