<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceReportConfiguration;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SRCRequest;

class ServiceReportConfigurationController extends Controller
{
    //
    public function index()
    {
        $service_report_configuration = DB::table("service_report_configurations as src")
            ->leftJoin("services as s", "src.service_id", "=", "s.id")
            ->leftJoin("service_reports as sr", "src.service_report_id", "=", "sr.id")
            ->select("s.service_name", "sr.report_name", "src.id", "src.service_report_id", "src.service_id")
            ->get();

        if (empty($service_report_configuration)) {
            return response()->json([
                "success" => false,
                "message" => "No configurations found"
            ]);
        } else {
            return response()->json([
                "success" => true,
                "data" => $service_report_configuration,
            ]);
        }
    }

    public function store(SRCRequest $request)
    {
        $check_if_exists = ServiceReportConfiguration::where("service_id", "=", $request->service_id)
            ->where("service_report_id", "=", $request->service_report_id)
            ->get();

        if (count($check_if_exists) > 0) {
            return response()->json([
                "success" => false,
                "message" => "The report is already saved under the service."
            ], 400);
        }

        $new_config = new ServiceReportConfiguration();
        $new_config->service_id = $request->service_id;
        $new_config->service_report_id = $request->service_report_id;

        try {
            if ($new_config->save()) {
                return response()->json([
                    "success" => true,
                    "message" => "Service report added"
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong",
                "error" => $th->errorInfo[0],
            ], 400);
        }
    }

    public function destroy($id)
    {
        $src = ServiceReportConfiguration::find($id);

        try {
            if (!empty($src)) {
                if ($src->delete()) {
                    return response()->json([
                        "success" => true,
                        "message" => "Service report configuration deleted"
                    ], 200);
                } else {
                    return response()->json([
                        "success" => false,
                        "message" => "Service report configuration is NOT deleted"
                    ], 400);
                }
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Service report configuration NOT found"
                ], 404);
            }
        } catch (\Throwable $th) {
            throw response()->json([
                "success" => false,
                "message" => "Something went wrong",
                "error" => $th,
            ], 400);
        }
    }
}
