<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class FreelancerCertificate extends Model
{
    use HasTranslations;

    protected $fillable = ['user_id', 'file_name', 'file_path'];

    protected $with = ['translation'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function translations()
    {
        return $this->hasMany(FreelancerCertificateTranslation::class);
    }
}
