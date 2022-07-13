<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $services = Service::all();

        if (count($services) === 0) {
            return response()->json([
                "success" => false,
                "message" => "No services found"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "data" => $services
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
        $val_req = "required|min:3";
        $val_is_req = "required";
        $this->validate($request, [
            "service_name" => $val_req,
            "service_description" => $val_req,
            "availability" => $val_is_req,
            "not_available_text" => $val_req,
            "price" => $val_is_req
        ]);

        $new_service = new Service();
        $new_service->service_name = $request->service_name;
        $new_service->service_description = $request->service_description;
        $new_service->availability = $request->availability;
        $new_service->not_available_text = $request->not_available_text;
        $new_service->price = $request->price;

        if ($new_service->save()) {
            return response()->json([
                "success" => true,
                "message" => "New service created"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Service not created"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
        return response()->json([
            "success" => true,
            "data" => $service
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        //
        $val_req = "required|min:3";
        $val_is_req = "required";
        $this->validate($request, [
            "service_name" => $val_req,
            "service_description" => $val_req,
            "availability" => $val_is_req,
            "not_available_text" => $val_req,
            "price" => $val_is_req
        ]);

        $service->service_name = $request->service_name;
        $service->service_description = $request->service_description;
        $service->availability = $request->availability;
        $service->not_available_text = $request->not_available_text;
        $service->price = $request->price;

        if ($service->save()) {
            return response()->json([
                "success" => true,
                "message" => "Service updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Service could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
        if ($service->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Service deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Service could not be deleted"
            ], 400);
        }
    }
}
