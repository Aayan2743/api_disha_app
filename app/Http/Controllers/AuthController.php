<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * AdminRegister API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => 'admin',
            'password' => Hash::make($request->password),
        ]);

        // Generate JWT Token
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'status'  => true,
            'message' => 'User Registered Successfully',
            'token'   => $token,
            'data'    => $user,
        ], 201);
    }

    /**
     * Admin Login API
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Check login type
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        // Only Admin Login
        $credentials = [
            $field     => $request->login,
            'password' => $request->password,
            'role'     => 'admin', // Admin
        ];

        if (! $token = auth()->attempt($credentials)) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid Admin Credentials',
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Admin Login Successfully',
            'token'   => $token,
            'user'    => auth()->user(),
        ], 200);
    }

    /**
     * Telecaller Login API
     */

    public function telecallerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Check login type
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        // Only Telecaller Login
        $credentials = [
            $field     => $request->login,
            'password' => $request->password,
            'role'     => 'telecaller', // Telecaller
        ];

        if (! $token = auth()->attempt($credentials)) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid Telecaller Credentials',
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Telecaller Login Successfully',
            'token'   => $token,
            'user'    => auth()->user(),
        ], 200);
    }

    /**
     * Receptionist Login API
     */

    public function receptionistLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Check login type
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        // Only Receptionist Login
        $credentials = [
            $field     => $request->login,
            'password' => $request->password,
            'role'     => 'receptionist', // Receptionist
        ];

        if (! $token = auth()->attempt($credentials)) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid Receptionist Credentials',    
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Receptionist Login Successfully',
            'token'   => $token,
            'user'    => auth()->user(),
        ], 200);
    }

}