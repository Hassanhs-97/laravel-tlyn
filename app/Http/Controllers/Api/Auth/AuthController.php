<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {

        $input             = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $user              = User::create($input);
        $success['token']  = $user->createToken('MyApp')->plainTextToken;
        $success['name']   = $user->name;

        return response()->json(['message' => __('success'), 'data' => $success] , Response::HTTP_CREATED);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user             = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name']  = $user->name;

            return response()->json(['message' => __('success'), 'data' => $success], Response::HTTP_OK);
        } else {
            return response()->json(['message' => __('Unauthorised'), 'data' => null], Response::HTTP_UNAUTHORIZED);
        }
    }
}
