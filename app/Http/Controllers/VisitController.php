<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Discount;
use App\Models\Service;
use App\Models\ServiceReportConfiguration;
use App\Models\ServiceReportRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $visits = DB::table('visits')
            ->leftJoin('clients', 'visits.client_id', '=', 'clients.id')
            ->leftJoin('visit_types', 'visits.visit_type', '=', 'visit_types.id')
            ->leftJoin('discounts', 'visits.discount_type', '=', 'discounts.id')
            ->leftJoin('user_profiles as hd', 'visits.hd_representative', '=', 'hd.user_id')
            ->leftJoin('user_profiles as wc', 'visits.wc_representative', '=', 'wc.user_id')
            ->select('visits.*', 'clients.first_name', 'clients.middle_name', 'clients.last_name', 'clients.client_id', 'visit_types.type_name', 'discounts.discount_name', 'discounts.discount_type', 'discounts.discount_amount', 'hd.first_name as hd_first_name', 'hd.last_name as hd_last_name', 'wc.first_name as wc_first_name', 'wc.last_name as wc_last_name', 'clients.image')
            ->get();

        if (count($visits) === 0) {
            return response()->json([
                "success" => false,
                "message" => "No visits found"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "data" => $visits
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
        $frm_data = $request->frmData;
        $sel_services = $request->selectedServices;
        //
        $new_visit = new Visit();

        $new_visit->client_id = $frm_data['client_id'];
        $new_visit->visit_date = $frm_data['visit_date'];
        $new_visit->time_in = $frm_data['time_in'];
        $new_visit->time_out = $frm_data['time_out'];
        $new_visit->service_id = json_encode($sel_services);
        $new_visit->visit_type = $frm_data['visit_type'];
        $new_visit->visit_type_fee = $frm_data['visit_type_fee'];

        $services_price = Service::select('price')
            ->where(function ($query) use ($sel_services) {
                for ($i = 0; $i < count($sel_services); $i++) {
                    $query->orWhere('id', '=', $sel_services[$i]);
                }
            })
            ->get();

        $service_price_total = 0;
        for ($i = 0; $i < count($services_price); $i++) {
            $service_price_total += $services_price[$i]->price;
        }

        $new_visit->subtotal = $service_price_total;

        $discount_calc = Discount::where('id', '=', $frm_data['discount_type'])->get();
        $discount_calc = $discount_calc[0]->discount_type === "1" ? $discount_calc[0]->discount_amount / 100 : $discount_calc[0]->discount_amount;

        $discount_calc = $service_price_total * $discount_calc;

        $new_visit->discount_type = $frm_data['discount_type'];
        $new_visit->discount_amount = $discount_calc;

        $new_visit->discount_others = 0;
        $new_visit->discount_type_others = 0;

        $curr_total_amount = $service_price_total - $discount_calc + floatval($frm_data['visit_type_fee']);
        $new_visit->total_amount = $curr_total_amount;

        $new_visit->points = $curr_total_amount / 50;
        $new_visit->hd_representative = $frm_data['hd_representative'];
        $new_visit->wc_representative = $frm_data['wc_representative'];

        if ($new_visit->save()) {
            return response()->json([
                "success" => true,
                "message" => "New visit created"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "New visit not created"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function show(Visit $visit)
    {
        //

        $visit_detail = DB::table('visits')
            ->leftJoin('clients', 'visits.client_id', '=', 'clients.id')
            ->leftJoin('visit_types', 'visits.visit_type', '=', 'visit_types.id')
            ->leftJoin('discounts', 'visits.discount_type', '=', 'discounts.id')
            ->leftJoin('user_profiles as hd', 'visits.hd_representative', '=', 'hd.user_id')
            ->leftJoin('user_profiles as wc', 'visits.wc_representative', '=', 'wc.user_id')
            ->where('visits.id', '=', $visit->id)
            ->select('visits.*', 'clients.id as client', 'clients.first_name', 'clients.middle_name', 'clients.last_name', 'clients.client_id', 'visit_types.type_name', 'discounts.discount_name', 'discounts.discount_type', 'discounts.discount_amount as discount_discount_amount', 'hd.first_name as hd_first_name', 'hd.last_name as hd_last_name', 'wc.first_name as wc_first_name', 'wc.last_name as wc_last_name', 'clients.image')
            ->get();

        if (count($visit_detail) <= 0) {
            return response()->json([
                "success" => false,
                "message" => "No visits found"
            ], 200);
        } else {
            $service_report = json_decode($visit_detail[0]->service_id);

            $get_service_reports = ServiceReportConfiguration::orWhere(function ($query) use ($service_report) {
                foreach ($service_report as $sr) {
                    $query->orWhere("service_report_configurations.service_id", "=", $sr);
                }
            })
                ->leftJoin('service_reports as sr', 'sr.id', '=', 'service_report_configurations.service_report_id')
                ->select('service_report_configurations.*', 'sr.form_name', 'sr.report_name', 'service_report_configurations.service_report_id')
                ->get();

            return response()->json([
                "success" => true,
                "data" => $visit_detail,
                "reports" => $get_service_reports
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visit $visit)
    {
        //
        $this->validate($request, [
            "client_id" => "required",
            "visit_date" => "required|date",
            "time_in" => "required",
            "time_out" => "required",
            "service_id" => "required",
            "visit_type" => "required",
            "visit_type_fee" => "required",
            "subtotal" => "required",
            "discount_type" => "required",
            "discount_amount" => "required",
            "discount_type" => "required",
            "discount_others" => "required",
            "total_amount" => "required",
            "points" => "required",
            "hd_representative" => "required",
            "wc_representative" => "required"
        ]);

        $visit->client_id = $request->client_id;
        $visit->visit_date = $request->visit_date;
        $visit->time_in = $request->time_in;
        $visit->time_out = $request->time_out;
        $visit->service_id = $request->service_id;
        $visit->visit_type = $request->visit_type;
        $visit->visit_type_fee = $request->visit_type_fee;
        $visit->subtotal = $request->subtotal;
        $visit->discount_type = $request->discount_type;
        $visit->discount_amount = $request->discount_amount;
        $visit->discount_type = $request->discount_type;
        $visit->discount_others = $request->discount_others;
        $visit->total_amount = $request->total_amount;
        $visit->points = $request->points;
        $visit->hd_representative = $request->hd_representative;
        $visit->wc_representative = $request->wc_representative;

        if ($visit->save()) {
            return response()->json([
                "success" => true,
                "message" => "Visit updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Visit could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visit $visit)
    {
        //
        if ($visit->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Visit deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Visit could not be deleted"
            ], 400);
        }
    }
}
