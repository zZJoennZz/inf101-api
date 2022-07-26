<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReportRecord extends Model
{
    use HasFactory;
    protected $fillable = ['visit_id', 'report_id', 'record', 'added_by'];
}
