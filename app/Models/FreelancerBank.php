<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class FreelancerBank extends Model
{
    protected $fillable = [
        'freelancer_id',
        'bank_name',
        'account_number',
        'iban',
        'swift_code',
    ];

    protected $casts = [
        'freelancer_id' => 'integer',
    ];

    // public function setAccountNumberAttribute($value)
    // {
    //     $this->attributes['account_number'] = Crypt::encryptString($value);
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
