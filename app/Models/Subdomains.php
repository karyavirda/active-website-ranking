<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdomains extends Model
{
    protected $fillable = [
        'subdomain',
        'paket'
    ];
}
