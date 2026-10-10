<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriteriaWeights extends Model
{
    protected $table = 'criteria_weights';

    protected $fillable = [
        'criteria_id',
        'bobot',
    ];

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criterias::class, 'criteria_id');
    }
}
