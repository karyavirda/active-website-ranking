<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataImages extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'subdomain',
        'nama',
        'created_at'
    ];
}
