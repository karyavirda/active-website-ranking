<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataParts extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id', // Sertakan ini agar bisa diinput manual
        'subdomain',
        'judul',
        'tipe',
        'created_at'
    ];
}
