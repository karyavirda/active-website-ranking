<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataLogs extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id', // Sertakan ini agar bisa diinput manual
        'subdomain',
        'nama_admin',
        'aktivitas',
        'activity_date'
    ];
}
