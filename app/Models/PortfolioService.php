<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioService extends Model
{
    
    protected $fillable = ['service_id', 'portfolio_id'];

    protected $casts = [
        'service_id'   => 'integer',
        'portfolio_id' => 'integer',
    ];
}
