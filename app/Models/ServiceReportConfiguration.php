<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReportConfiguration extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'service_report_id'];
}
