<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;

class ClientController extends Controller
{
    //
    public function index()
    {
        $clients = Client::select('first_name', 'middle_name', 'last_name', 'client_id', 'id', 'image')->get();

        if (json_encode($clients) === json_encode([])) {
            return response()->json([
                "success" => false,
                "message" => "NO clients found"
            ], 400);
        }
        $data = array();

        foreach ($clients as $client) {
            array_push($data, [
                "first_name" => $client->first_name,
                "middle_name" => $client->middle_name,
                "last_name" => $client->last_name,
                "image" => $client->image,
                "client_id" => $client->client_id,
                "id" => $client->id,
            ]);
        }
        return response()->json([
            "success" => true,
            "data" => $data
        ], 200);
    }

    public function show($id)
    {
        $client = Client::find($id);

        $visits = DB::table('visits')
            ->leftJoin('clients', 'visits.client_id', '=', 'clients.id')
            ->leftJoin('visit_types', 'visits.visit_type', '=', 'visit_types.id')
            ->leftJoin('discounts', 'visits.discount_type', '=', 'discounts.id')
            ->leftJoin('user_profiles as hd', 'visits.hd_representative', '=', 'hd.id')
            ->leftJoin('user_profiles as wc', 'visits.wc_representative', '=', 'wc.id')
            ->select('visits.*', 'clients.first_name', 'clients.middle_name', 'clients.last_name', 'clients.client_id', 'visit_types.type_name', 'discounts.discount_name', 'discounts.discount_type', 'discounts.discount_amount', 'hd.first_name as hd_first_name', 'hd.last_name as hd_last_name', 'wc.first_name as wc_first_name', 'wc.last_name as wc_last_name', 'clients.image')
            ->where('clients.id', '=', $id)
            ->get();

        if (json_encode($client) === json_encode([]) || is_null($client)) {
            return response()->json([
                "success" => false,
                "message" => "Client NOT found"
            ], 400);
        }

        return response()->json([
            "success" => true,
            "data" => $client,
            "visit" => $visits
        ], 200);
    }

    public function store(Request $request)
    {
        if (!$request->hasFile('sig') || !$request->hasFile('image')) {
            return response()->json([
                "success" => false,
                "message" => "No signature/picture image attached"
            ], 400);
        }

        $client = Client::orderBy('id', 'desc')->first();

        $clientId = "";
        if (is_null($client)) {
            $clientId = "101-01";
        } else {
            $clientId = $client->id >= 10 ? "101-" . $client->id + 1 : "101-0" . $client->id + 1;
        }

        $imgStorePath = "uploads/img";
        $sigStorePath = "uploads/sig";

        $get_img = $request->file('image');
        $get_sig = $request->file('sig');

        $imgFileName = $get_img->store($imgStorePath, "public");
        $sigFileName = $get_sig->store($sigStorePath, "public");

        $field_req = 'required|min:2';
        $validator = Validator::make($request->all(), [
            'first_name' => $field_req,
            'middle_name' => $field_req,
            'last_name' => $field_req,
            'gender' => 'required',
            'birthday' => 'required|date',
            'address' => 'required|min:3',
            'barangay' => $field_req,
            'city' => $field_req,
            'province' => $field_req,
            'region' => $field_req,
            'zip_code' => $field_req,
            'contact_number' => $field_req,
            'email_address' => 'required|email|min:2',
            'maintenance' => $field_req,
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
        $client->signature = $sigFileName;
        $client->image = $imgFileName;
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

    public function update(Request $request, $id)
    {
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

    public function destroy($id)
    {
        $client = Client::find($id);
        $public = 'public/';
        if (Storage::exists($public . $client->image) && Storage::exists($public . $client->signature)) {
            Storage::delete($public . $client->image);
            Storage::delete($public . $client->signature);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Client profile could NOT be deleted. Image and signature file not found"
            ], 400);
        }

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

    public function search_client(Request $request)
    {
        $search_query = $request->search_query;

        $search_result = Client::where('client_id', 'LIKE', '%' . $search_query . '%')->orWhere('first_name', 'LIKE', '%' . $search_query . '%')->orWhere('middle_name', 'LIKE', '%' . $search_query . '%')->orWhere('last_name', 'LIKE', '%' . $search_query . '%')->limit(5)->select('id', 'client_id', 'first_name', 'middle_name', 'last_name', 'suffix')->get();

        if (count($search_result) > 5) {
            return response()->json([
                "success" => true,
                "message" => "Only 5 results. Please enter more specific information.",
                "data" => $search_result
            ], 200);
        }

        return response()->json([
            "success" => true,
            "data" => $search_result
        ], 200);
    }
}
