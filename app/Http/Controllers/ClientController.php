<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;

class ClientController extends Controller
{
    //
    public function index() {
        $clients = Client::all();

        if (json_encode($clients) === json_encode([])) {
            return response()->json([
                "success" => false,
                "message" => "NO clients found"
            ], 400);
        }

        return response()->json([
            "success" => true,
            "data" => $clients
        ], 200);
    }

    public function show($id) {
        $client = Client::find($id);
        
        if (json_encode($client) === json_encode([]) || is_null($client)) {
            return response()->json([
                "success" => false,
                "message" => "Client NOT found"
            ], 400);
        }

        return response()->json([
            "success" => true,
            "data" => $client
        ], 200);
    }

    public function store(Request $request) {
        $client = Client::orderBy('id', 'desc')->first(); 
        $clientId;
        if (is_null($client)) {
            $clientId = "101-01";
        } else {
            $clientId = $client->id >= 10 ? "101-" . $client->id + 1 : "101-0" . $client->id + 1;
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'gender' => 'required',
            'birthday' => 'required|date',
            'address' => 'required|min:3',
            'barangay' => 'required|min:2',
            'city' => 'required|min:2',
            'province' => 'required|min:2',
            'region' => 'required|min:2',
            'zip_code' => 'required|min:2',
            'contact_number' => 'required|min:2',
            'email_address' => 'required|email|min:2',
            'maintenance' => 'required|min:2',
            'signature' => 'required|min:2',
            'image' => 'required|min:2',
        ]);

        $errors = $validator->errors();
        
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $errors 
            ], 400);
        }

        $client = new Client();
        $client->client_id = $clientId;
        $client->first_name = $request->first_name;
        $client->middle_name = $request->middle_name;
        $client->last_name = $request->last_name;
        $client->suffix = $request->suffix;
        $client->gender = $request->gender;
        $client->birthday = $request->birthday;
        $client->address = $request->address;
        $client->barangay = $request->barangay;
        $client->city = $request->city;
        $client->province = $request->province;
        $client->region = $request->region;
        $client->zip_code = $request->zip_code;
        $client->contact_number = $request->contact_number;
        $client->email_address = $request->email_address;
        $client->facebook = $request->facebook;
        $client->instagram = $request->instagram;
        $client->maintenance = $request->maintenance;
        $client->signature = $request->signature;
        $client->image = $request->image;
        $client->added_by = $request->user()->id;

        if ($client->save()) {
            return response()->json([
                "success" => true,
                "message" => "Client profile successfully saved"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Client profile could NOT be saved"
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $client = Client::find($id);

        if (is_null($client)) {
            return response()->json([
                "success" => false,
                "message" => "Client profile could NOT be found"
            ], 400);
        }

        $updated = $client->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                "success" => true,
                "message" => "Client profile changes saved"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Client profile changes could NOT be saved"
            ], 500);
        }
    }

    public function destroy($id) {
        $client = Client::find($id);

        if (is_null($client)) {
            return response()->json([
                "success" => false,
                "message" => "Client profile could NOT be found"
            ], 400);
        }

        if ($client->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Client profile successfully deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Client profile could NOT be deleted"
            ], 500);
        }
    }

    public function edit($id)
    {
        //
    }

    public function create()
    {
        //
    }
}
