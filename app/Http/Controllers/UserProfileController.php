<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return \Illuminate\Http\Response
     */
    public function show(UserProfile $userProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserProfile  $userProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserProfile $userProfile)
    {
        //
        if (!is_null($request->first_name)) {
            $userProfile->first_name = $request->first_name;
        }
        if (!is_null($request->middle_name)) {
            $userProfile->middle_name = $request->middle_name;
        }
        if (!is_null($request->last_name)) {
            $userProfile->last_name = $request->last_name;
        }
        if (!is_null($request->contact_number)) {
            $userProfile->contact_number = $request->contact_number;
        }

        if ($userProfile->save()) {
            return response()->json([
                "success" => true,
                "message" => "User profile successfully updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "User profile could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserProfile $userProfile)
    {
        //
    }
}
