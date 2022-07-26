<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceReportRecord;

class ServiceReportRecordController extends Controller
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
        $new_record = new ServiceReportRecord();
        $new_record->visit_id = $request->visit_id;
        $new_record->report_id = $request->report_id;
        $new_record->record = json_encode($request->record);
        $new_record->added_by = $request->user()->id;

        if ($new_record->save()) {
            return response()->json([
                "success" => true,
                "message" => "Report record successfully saved"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "message" => "Report record could not be saved"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($visit_id, $report_id, $client_id)
    {
        //
        $service_report_record = ServiceReportRecord::leftJoin('visits as v', 'v.id', '=', 'service_report_records.visit_id')
            ->select('service_report_records.*')
            ->where('v.id', '=', $visit_id)
            ->where('service_report_records.report_id', '=', $report_id)
            ->where('v.client_id', '=', $client_id)
            ->get();

        if (count($service_report_record) <= 0) {
            return response()->json([
                "success" => false,
                "message" => "No records found"
            ], 404);
        } else {
            return response()->json([
                "success" => true,
                "data" => $service_report_record
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $updated = ServiceReportRecord::find($id);
        $updated->record = $request->record;

        if ($updated->save()) {
            return response()->json([
                "success" => true,
                "message" => "Report record successfully updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Report record could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
