<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataNews extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id', // Sertakan ini agar bisa diinput manual
        'subdomain',
        'judul',
        'created_at'
    ];
}
