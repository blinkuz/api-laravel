<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        if ($user->save()) {
            return response()->json([
                'success' => true,
                'product' => $user
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user could not be register'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function login(Request $request){
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ], Response::HTTP_OK);
    }

    public function getAll(){
        $users = User::All()->toArray();
        return response()->json([
            'success' => true,
            'users' => $users
        ], Response::HTTP_OK);
    }
}
