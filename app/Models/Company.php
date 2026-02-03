<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasTranslations;

    protected $fillable = [
        'logo',
        'registration_number',
        'contact_email',
        'contact_phone_number',
        'website_url',
        'is_verified',
    ];

    protected $with = ['translation'];

    public function translations()
    {
        return $this->hasMany(CompanyTranslation::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(CompanySocialLink::class);
    }
    public function freelancers()
    {
        return $this->hasMany(Freelancer::class);
    }
}
