<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'language',
        'title',
        'description'
    ];

    /**
     * Get the plan that owns the translation.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
