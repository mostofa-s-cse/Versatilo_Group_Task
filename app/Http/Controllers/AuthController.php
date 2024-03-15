<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /*-------------------------------------------
     * Sign Up The User
     -------------------------------------------*/
    public function register(Request $request)
    {
        try {
            // Validation
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            // If validation fails
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'data' => [
                    'token' => $user->createToken("API TOKEN")->plainTextToken,
                    'user' => $user
                ]
            ], 200);
        } catch (\Throwable $th) {
            // Handle exceptions
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    /*-------------------------------------------
     * Login The User
     -------------------------------------------*/
    public function login(Request $request)
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 401);
            }

            // Attempt to authenticate user
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & password do not match our records.',
                ], 401);
            }

            // Fetch authenticated user details
            $user = User::where('email', $request->email)
                ->first(['id', 'name', 'email']);

            // Generate and return API token along with user details
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'data' => [
                    'token' => $user->createToken("API TOKEN")->plainTextToken,
                    'user' => $user,
                ],
            ], 200);
        } catch (\Throwable $th) {
            // Handle exceptions and return error response
            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    /*-------------------------------------------
     * Current User
     -------------------------------------------*/
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /*-------------------------------------------
     * logout The User
     -------------------------------------------*/
   public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
        'message' => 'Successfully logged out'
        ]);

    }

    
}
