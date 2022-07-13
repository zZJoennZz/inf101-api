<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{

    public function get_all()
    {
        $users = User::select('first_name', 'last_name', 'id', 'created_at', 'updated_at')->get();

        if (count($users) === 0) {
            return response()->json([
                "success" => false,
                "message" => "No users found"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "data" => $users
            ], 200);
        }
    }

    //
    public function signin(Request $request)
    {
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
            return response()->json([
                "success" => false,
                "message" => "Unauthorized"
            ], 401);
        }
    }

    public function add_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'min:2',
            'last_name' => 'min:2',
            'email' => 'required|email',
            'username' => 'required|min:6',
            'password' => 'required|min:4',
        ]);

        $errors = $validator->errors();

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $errors
            ], 400);
        }

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

    //signout the user
    public function signout(Request $request)
    {
        //get the tokens
        //$tokenRepository = app(TokenRepository::class);
        //check the status of the user if logged in or not
        $is_logged_in = auth()->guard('api')->check();
        //get token of the user
        //$token = auth()->user()->token();    

        //check if the user is logged in and revoke the token login access
        if ($is_logged_in) {
            $request->user()->token()->revoke();
            return response()->json([
                'success' => true,
                'message' => 'Token access revoked',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed'
            ], 401);
        }
    }

    public function validate_token(Request $request)
    {
        $is_logged_in = auth()->guard('api')->check();

        if ($is_logged_in) {
            return response()->json([
                'success' => true,
                'message' => 'Token have access',
                'userId' => $request->user()->id,
                'username' => $request->user()->username
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Your token have no access'
            ], 401);
        }
    }
}
