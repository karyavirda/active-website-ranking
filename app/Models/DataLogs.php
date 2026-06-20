<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataLogs extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'subdomain',
        'nama_admin',
        'aktivitas',
        'activity_date'
    ];
}
