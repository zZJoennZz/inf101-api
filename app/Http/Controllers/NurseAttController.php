<?php

namespace App\Http\Controllers;

use App\Models\NurseAttendant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class NurseAttController extends Controller
{

    public function index() {
        //
        $na = NurseAttendant::all();

        if (json_encode($na) === json_encode([]) || is_null($na)) {
            return response()->json([
                "success" => false,
                "message" => "NO nurse attendant found"
            ], 400);
        }

        return response()->json([
            "success" => true,
            "data" => $na
        ], 200);
    }

    public function store(Request $request) {
        //
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'middle_name' => 'required|min:2',
            'last_name' => 'required|min:2',
        ]);

        $errors = $validator->errors();
        
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $errors 
            ], 400);
        }

        $na = new NurseAttendant();
        $na->first_name = $request->first_name;
        $na->middle_name = $request->middle_name;
        $na->last_name = $request->last_name;

        if ($na->save()) {
            return response()->json([
                "success" => true,
                "message" => "Nurse attendant record saved"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant record could NOT be saved"
            ], 500);
        }
    }

    public function show($id) {
        //
        $na = NurseAttendant::find($id);
        
        if (json_encode($na) === json_encode([]) || is_null($na)) {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant NOT found"
            ], 400);
        }

        return response()->json([
            "success" => true,
            "data" => $client
        ], 200);
    }

    public function update(Request $request, $id) {
        //
        $na = NurseAttendant::find($id);

        if (is_null($na)) {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant could NOT be found"
            ], 400);
        }

        $updated = $na->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                "success" => true,
                "message" => "Nurse attendant record changes saved"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant record changes could NOT be saved"
            ], 500);
        }
    }

    public function destroy($id) {
        //
        $na = NurseAttendant::find($id);

        if (is_null($na)) {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant record could NOT be found"
            ], 400);
        }

        if ($na->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Nurse attendant record successfully deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Nurse attendant record could NOT be deleted"
            ], 500);
        }
    }
}
