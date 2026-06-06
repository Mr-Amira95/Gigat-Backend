<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'service_id',
        'rating',
    ];

    protected $casts = [
        'user_id'    => 'integer',
        'service_id' => 'integer',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class);
    }
    /**
     * Get the service that owns the review.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
