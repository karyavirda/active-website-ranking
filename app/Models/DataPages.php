<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPages extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'subdomain',
        'judul',
        'tipe',
        'created_at'
    ];
}
