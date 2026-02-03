<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationComment extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'quotation_id',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(QuotationCommentTranslation::class);
    }
    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the Quotation model
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
