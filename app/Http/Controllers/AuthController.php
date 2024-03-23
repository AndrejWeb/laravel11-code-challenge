<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $this->validateUser($request, [
                'name' => 'nullable|min:2',
                'email' => 'required|email|min:6',
                'password' => 'required|min:6',
            ]);

            // Check if user already exists
            $user = User::where('email', $request->email)->first();
            if($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with email address ' . $request->email . ' already exists. Please use another email address.',
                ], 409);
            }

            // Create user and issue token
            $user = User::create([
               'name' => $request->name ?? null,
               'email' => $request->email,
               'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'token' => $user->createToken("API Token")->plainTextToken,
            ], 200);

        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate user
            $this->validateUser($request, [
                'email' => 'required|email|min:6',
                'password' => 'required|min:6',
            ]);

            if(!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect login details.'
                ], 404);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'token' => $user->createToken("API Token")->plainTextToken,
            ]);

        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->validateUser($request, [
            'email' => 'required|email|min:6',
        ]);

        // If user does not exist
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist',
            ], 404);
        }

        $tokens = $user->tokens;
        if($tokens) {
            foreach($tokens as $token) {
                $token->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ],200);
    }

    /**
     * Validate user
     *
     * @param Request $request
     * @param array $rules
     * @return JsonResponse|void
     */
    protected function validateUser(Request $request, array $rules)
    {
        $validateUser = Validator::make($request->all(), $rules);

        if($validateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
            ], 401);
        }
    }
}
