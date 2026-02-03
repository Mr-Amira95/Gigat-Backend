<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerCertificateTranslation extends Model
{
      protected $fillable = [
        'freelancer_certificate_id',
        'language',
        'description',
    ];


    public function certificate()
    {
        return $this->belongsTo(FreelancerCertificate::class);
    }
}
