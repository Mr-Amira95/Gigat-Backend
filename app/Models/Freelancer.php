<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Freelancer extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'user_id',
        'status',
        'company_id',

    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'user_id'    => 'integer',
        'company_id' => 'integer',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(FreelancerTranslation::class);
    }
    /**
     * Get the user that owns the freelancer profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the languages for the freelancer.
     */
    public function languages()
    {
        return $this->hasMany(UserLanguage::class);
    }

    /**
     * Scope a query to only include freelancers with specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
