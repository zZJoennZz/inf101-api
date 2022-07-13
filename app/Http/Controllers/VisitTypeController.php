<?php

namespace App\Http\Controllers;

use App\Models\VisitType;
use Illuminate\Http\Request;

class VisitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $visit_type = VisitType::all();

        if (count($visit_type) === 0) {
            return response()->json([
                "success" => false,
                "message" => "No visit type found"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "data" => $visit_type
            ], 200);
        }
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
        $this->validate($request, [
            "type_name" => "required|min:3"
        ]);

        $new_visit_type = new VisitType();
        $new_visit_type->type_name = $request->type_name;

        if ($new_visit_type->save()) {
            return response()->json([
                "success" => true,
                "message" => "Visit type saved"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Visit type could not be saved"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function show(VisitType $visitType)
    {
        //
        return response()->json([
            "success" => true,
            "data" => $visitType
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VisitType $visitType)
    {
        //
        $this->validate($request, [
            "type_name" => "required|min:3"
        ]);

        $visitType->type_name = $request->type_name;

        if ($visitType->save()) {
            return response()->json([
                "success" => true,
                "message" => "Visit type updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Visit type could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function destroy(VisitType $visitType)
    {
        //
        if ($visitType->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Visit type deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Visit type could not be deleted"
            ], 400);
        }
    }
}
