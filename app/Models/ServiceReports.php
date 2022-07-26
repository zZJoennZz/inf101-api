<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReports extends Model
{
    use HasFactory;

    protected $fillable = ['report_name', 'description', 'fields'];
}
