<?php

namespace App\Http\Controllers;

use App\Models\ServiceReports;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceReportRequest;

class ServiceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $reports = ServiceReports::all();

        if (count($reports) >= 1) {
            return response()->json([
                "success" => true,
                "data" => $reports
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "No service reports found"
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceReportRequest $request)
    {
        //
        $new_report = new ServiceReports();
        $new_report->report_name = $request->report_name;
        $new_report->description = $request->description;
        $new_report->fields = $request->fields;

        if ($new_report->save()) {
            return response()->json([
                "success" => true,
                "message" => "New service report created",
                "data" => $new_report,
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Service report could not be created"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\servicereports  $servicereports
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceReports $ServiceReports, $id)
    {
        $data = $ServiceReports->find($id);
        if ($data === null) {
            return response()->json([
                "success" => false,
                "message" => "Service report could not be found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "data" => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\servicereports  $servicereports
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceReportRequest $request, ServiceReports $servicereports, $id)
    {
        //
        $data = $servicereports->find($id);

        if ($data === null) {
            return response()->json([
                "success" => false,
                "message" => "Service report could not be found"
            ], 404);
        }

        $updated = $data;
        $updated->report_name = $request->report_name;
        $updated->description = $request->description;
        $updated->fields = $request->fields;

        if ($updated->save()) {
            return response()->json([
                "success" => true,
                "message" => "Service report changes saved",
                "data" => $updated
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong, changes could not be saved"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\servicereports  $servicereports
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceReports $servicereports, $id)
    {
        //
        $data = $servicereports->find($id);
        if ($data === null) {
            return response()->json([
                "success" => false,
                "message" => "Service report could not be found"
            ], 404);
        }

        if ($data->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Service report successfully deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Service report could not be deleted"
            ], 400);
        }
    }
}
