<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'visit_date', 'time_in', 'time_out', 'service_id', 'visit_type', 'visit_type_fee', 'subtotal', 'discount_type', 'discount_amount', 'discount_type', 'discount_others', 'total_amount', 'points', 'hd_representative', 'wc_representative'
    ];
}
