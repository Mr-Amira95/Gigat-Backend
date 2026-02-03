<?php

namespace App\Models;

use App\Traits\BlockFilterTrait;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory, BlockFilterTrait, HasTranslations;

    protected $fillable = [
        'sub_category_id',
        'price',
        'delivery_day',
        'revisions',
        'source_file',
        'user_id',
        'status'
    ];
    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(QuotationTranslation::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function quotationComments()
    {
        return $this->hasMany(QuotationComment::class, 'quotation_id');
    }
    public function attachments()
    {
        return $this->hasMany(QuotationAttachment::class);
    }
}
