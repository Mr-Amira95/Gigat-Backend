<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioTranslation extends Model
{
    protected $fillable = ['portfolio_id', 'language', 'title', 'description'];
}
