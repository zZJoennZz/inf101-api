<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\DbCommand;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{

    public function get_all()
    {
        $users = User::select('up.first_name', 'up.last_name', 'users.id', 'users.created_at', 'users.updated_at')
            ->leftJoin('user_profiles as up', 'users.id', '=', 'up.user_id')
            ->get();

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

    public function get_all_users()
    {
        if (auth()->user()->is_admin !== 1) {
            return response()->json([
                "success" => false,
                "message" => "You are not allowed to access this"
            ], 401);
        }

        $users = User::select('up.first_name', 'up.middle_name', 'up.last_name', 'users.is_admin', 'users.is_active', 'users.id', 'users.created_at', 'users.updated_at')
            ->leftJoin('user_profiles as up', 'users.id', '=', 'up.user_id')
            ->get();

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
                "username" => $data['username'],
                "is_admin" => auth()->user()->is_admin
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
        $validate_name = 'required|min:2';
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|min:6|unique:users,username',
            'password' => 'required|min:4',
            'first_name' => $validate_name,
            'middle_name' => $validate_name,
            'last_name' => $validate_name,
        ]);

        $errors = $validator->errors();

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $errors
            ], 400);
        }

        $new_user = new User();
        $new_user->username = $request->username;
        $new_user->password = bcrypt($request->password);
        $new_user->email = $request->email;
        $result = $new_user->save();

        $new_user_profile = new UserProfile();
        $new_user_profile->first_name = $request->first_name;
        $new_user_profile->middle_name = $request->middle_name;
        $new_user_profile->last_name = $request->last_name;
        $new_user_profile->contact_number = $request->contact_number;
        $new_user_profile->user_id = $new_user->id;

        if ($result && $new_user_profile->save()) {
            return response()->json([
                "success" => true,
                "message" => "User successfully saved",
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "User is not created",
            ], 400);
        }
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

    public function get_my_account(Request $request)
    {
        $my_details = User::leftJoin('user_profiles as up', 'users.id', '=', 'up.user_id')
            ->select('users.username', 'users.email', 'users.account_type', 'up.first_name', 'up.middle_name', 'up.last_name', 'up.contact_number', 'up.id as up_id')
            ->where('users.id', '=', $request->user()->id)
            ->get();

        if (!empty($my_details)) {
            return response()->json([
                "success" => true,
                "message" => "Your account is fetched",
                "data" => $my_details
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Account not found"
            ], 404);
        }
    }

    public function update_my_account(Request $request)
    {
        $my_details = User::find($request->user()->id);
        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                "success" => false,
                "message" => "Password does not match record"
            ], 403);
        }

        if (!is_null(trim($request->username))) {
            $my_details->username = $request->username;
        }

        if (!is_null(trim($request->email))) {
            $my_details->email = $request->email;
        }

        if (trim($request->new_password) !== "" && $request->new_password === $request->confirm_password) {
            $my_details->password = bcrypt($request->new_password);
        }

        if ($my_details->save()) {
            return response()->json([
                "success" => true,
                "message" => "Login details updated successfully"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Login details failed to update"
            ], 400);
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
                'username' => $request->user()->username,
                'is_admin' => $request->user()->is_admin,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Your token have no access'
            ], 401);
        }
    }

    public function get_user($id)
    {
        try {
            $user = User::find($id);
            $user_profile = UserProfile::where("user_id", "=", $id)->get();

            if (empty($user) || empty($user_profile)) {
                return response()->json([
                    "success" => false,
                    "message" => "User account does not exist"
                ], 404);
            }

            return response()->json([
                "success" => true,
                "message" => "User found",
                "data" => $user,
                "profile" => $user_profile[0],
            ], 200);
        } catch (\Throwable $th) {
            error_log($th);
            throw $th;
        }
    }

    public function update_user(Request $request, $user_id, $profile_id)
    {
        if ($request->user()->is_admin !== 1 && $request->user()->is_active !== 1) {
            return response()->json([
                "success" => false,
                "message" => "You are not allowed to do this"
            ], 401);
        }

        $status = true;
        $current_user = $request->user()->id === $user_id;

        if ($current_user) {
            return response()->json([
                "success" => false,
                "message" => "You cannot edit your own account through this method"
            ], 401);
        }

        try {
            DB::beginTransaction();
            $user = User::find($user_id);

            if (trim($request->username) !== "") {
                $user->username = $request->username;
            }
            if (trim($request->email) !== "") {
                $user->email = $request->email;
            }
            if (trim($request->is_admin) !== "") {
                $user->is_admin = $request->is_admin;
            }
            if (trim($request->is_active) !== "") {
                $user->is_active = $request->is_active;
            }
            if (trim($request->password) !== "" && $request->password === $request->confirm_password) {
                $user->password = bcrypt($request->confirm_password);
            }



            $save_user = $user->save();

            $user_profile = UserProfile::find($profile_id);
            if (!is_null($request->first_name)) {
                $user_profile->first_name = $request->first_name;
            }
            if (!is_null($request->middle_name)) {
                $user_profile->middle_name = $request->middle_name;
            }
            if (!is_null($request->last_name)) {
                $user_profile->last_name = $request->last_name;
            }
            if (!is_null($request->contact_number)) {
                $user_profile->contact_number = $request->contact_number;
            }

            $save_profile = $user_profile->save();

            $status = $status && $save_user && $save_profile;
        } catch (\Throwable $th) {
            DB::rollBack();
            error_log($th);
            throw $th;
            // throw response()->json([
            //     "succes" => false,
            //     "message" => "Something went wrong, changes not saved",
            //     "error" => $th,
            // ], 400);
        }

        if ($status) {
            DB::commit();
            return response()->json([
                "success" => true,
                "message" => "Changes saved"
            ], 200);
        } else {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Changes is not saved"
            ], 400);
        }
    }

    public function confirm_password(Request $request)
    {
        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                "success" => false,
                "message" => "Password does not match record"
            ], 403);
        }

        return response()->json([
            "success" => true,
            "message" => "Password matched"
        ], 200);
    }
}
