<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function signin(Request $request) {
        $data = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken(env('SECRET_KEY'))->accessToken;
            $userId = auth()->user()->id;
            return response()->json([
                "success" => true,
                "token" => $token,
                "userId" => $userId,
            ]);
        } else {
            return reponse()->json([
                "success" => false,
                "message" => "Unauthorized"
            ], 401);
        }
    }

    public function add_user(Request $request) {
        $this->validate($request, [
            'first_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'required|email',
            'username' => 'required|min:6',
            'password' => 'required|min:4',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken(env('SECRET_KEY'))->accessToken;

        return response()->json([
            "success" => true,
            "message" => "User successfully saved",
            "token" => $token
        ], 200);
    }
}
