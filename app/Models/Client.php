<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'birthday',
        'address',
        'barangay',
        'city',
        'province',
        'region',
        'zip_code',
        'contact_number',
        'email_address',
        'facebook',
        'instagram',
        'maintenance',
        'signature',
        'image',
        'added_by',
    ];
}
