<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Visit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function dashboard_stats(Request $request)
    {
        $filter_mode = "";
        $total_client = 0;
        $total_visit = 0;
        $est_revenue = 0;

        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            $filter_mode = "range";
        } else {
            $filter_mode = "all";
        }
        if ($filter_mode === "range") {
            $total_client = Client::whereBetween('created_at', [$request->start_date, $request->end_date])->get()->count();
        } else {
            $total_client = Client::get()->count();
        }
        if ($filter_mode === "range") {

            $est_revenue = Visit::whereBetween('created_at', [$request->start_date, $request->end_date])->sum('total_amount');
            $total_visit = Visit::whereBetween('created_at', [$request->start_date, $request->end_date])->get()->count();
        } else {
            $est_revenue = Visit::sum('total_amount');
            $total_visit = Visit::get()->count();
        }

        return response()->json([
            "success" => true,
            "message" => "Data are fetched",
            "data" => [
                "client" => $total_client,
                "visit" => $total_visit,
                "revenue" => $est_revenue,
            ],
        ]);
    }
}
