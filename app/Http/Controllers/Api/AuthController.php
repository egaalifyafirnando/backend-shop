<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // RUN MIDDLEWARE WHEN CLASS ACCESSED FIRST TIME
        $this->middleware('auth:api')->except(['register', 'login']);
    }

    /**
     * register
     *
     * @param  mixed $request
     * @return void
     */
    public function register(Request $request)
    {
        // CREATE VALIDATION RULES WITH VALIDATOR
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers',
            'password' => 'required|confirmed',
        ]);

        // IF VALIDATOR FAILED(?)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // CREATE DATA CUSTOMER
        $customer = Customer::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        // CREATE TOKEN CUSTOMER
        $token = JWTAuth::fromUser($customer);

        // RETURN RESPONSE SUCCESS
        if ($customer) {
            return response()->json([
                'success'   => true,
                'user'      => $customer,
                'token'     => $token
            ], 201);
        }

        // RETURN RESPONSE FAILED
        return response()->json([
            'success' => false
        ], 409);
    }

    /**
     * login
     *
     * @param  mixed $request
     * @return void
     */
    public function login(Request $request)
    {
        // CREATE VALIDATION RULES WITH VALIDATOR
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // IF VALIDATOR FAILED(?)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // CREDENTIALS
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Email or Password is incorrect.'
            ], 401);
        }

        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api')->user(),
            'token'     => $token
        ], 201);
    }

    /**
     * getUser
     *
     * @return void
     */
    public function getUser()
    {
        return response()->json([
            'success'   => true,
            'user'      => auth()->user()
        ], 200);
    }
}
