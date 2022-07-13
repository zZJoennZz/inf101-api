<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $discounts = Discount::all();

        if (count($discounts) === 0) {
            return response()->json([
                "success" => false,
                "message" => "No discounts found"
            ], 200);
        } else {
            return response()->json([
                "success" => true,
                "data" => $discounts
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
        $val_req = "required";
        $this->validate($request, [
            "discount_name" => $val_req,
            "discount_type" => $val_req,
            "discount_amount" => $val_req
        ]);

        $new_discount = new Discount();
        $new_discount->discount_name = $request->discount_name;
        $new_discount->discount_type = $request->discount_type;
        $new_discount->discount_amount = $request->discount_amount;

        if ($new_discount->save()) {
            return response()->json([
                "success" => true,
                "message" => "Discount created"
            ], 201);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Discount not created"
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount)
    {
        //
        return response()->json([
            "success" => true,
            "data" => $discount
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Discount $discount)
    {
        //
        $val_req = "required|min:3";
        $this->validate($request, [
            "discount_name" => $val_req,
            "discount_type" => $val_req,
            "discount_amount" => $val_req
        ]);

        $discount->discount_name = $request->discount_name;
        $discount->discount_type = $request->discount_type;
        $discount->discount_amount = $request->discount_amount;

        if ($discount->save()) {
            return response()->json([
                "success" => true,
                "message" => "Discount updated"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Discount could not be updated"
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        //
        if ($discount->delete()) {
            return response()->json([
                "success" => true,
                "message" => "Discount deleted"
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Discount could not be deleted"
            ], 400);
        }
    }
}
