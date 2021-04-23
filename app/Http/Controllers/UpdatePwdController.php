<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class UpdatePwdController  extends Controller
{
    public function reqForgotPassword(Request $request){
        if(!$this->validEmail($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $this->sendEmail($request->email);
            return response()->json([
                'success' => true,
                'message' => 'Password reset mail has been sent.'
            ], Response::HTTP_OK);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request){
        if ($this->validateToken($request)->count() > 0) {
            $user = User::whereEmail($request->email)->first();
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            $this->validateToken($request)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.'
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email or token does not exist.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function viewExampleEmailToken($passwordToken){
        return response()->json([
            'success' => true,
            'passwordToken' => $passwordToken
        ], Response::HTTP_OK);
    }

    private function validateToken($request){
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->passwordToken
        ]);
    }

    private function sendEmail($email){
        $token = $this->createToken($email);
        Mail::to($email)->send(new SendMail($token));
    }

    private function validEmail($email) {
        return !!User::where('email', $email)->first();
    }

    private function createToken($email){
        $isToken = DB::table('password_resets')->where('email', $email)->first();

        if($isToken) {
            return $isToken->token;
        }

        $token = Str::random(80);;
        $this->saveToken($token, $email);
        return $token;
    }

    private function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }
}
