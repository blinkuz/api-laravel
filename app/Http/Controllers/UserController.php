<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ], Response::HTTP_CREATED);
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

    public function getFiltered($name, $email){
        $users = DB::table('users')->where([
            ['name', 'LIKE', '%'.$name.'%'],
            ['email', 'LIKE', '%'.$email.'%'],
        ])->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'users' => $users
        ], Response::HTTP_OK);
    }

    public function delete($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->delete()) {
            return response()->json([
                'success' => true
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be deleted'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(UpdateUserRequest $request, $id){
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user with id ' . $id . ' cannot be found'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $updated = $user->fill($request->all())->save();
            if ($updated) {
                return response()->json([
                    'success' => true
                ], Response::HTTP_OK);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'User could not be updated'
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'User could not be updated'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
