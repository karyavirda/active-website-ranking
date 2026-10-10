<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Criterias extends Model
{
    protected $table = 'criterias';

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'satuan',
        'deskripsi',
    ];

    public function weight(): HasOne
    {
        return $this->hasOne(CriteriaWeights::class, 'criteria_id');
    }
}
