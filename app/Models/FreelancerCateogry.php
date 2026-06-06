<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerCateogry extends Model
{
    protected $fillable = ['user_id', 'category_id'];

    protected $casts = [
        'user_id'     => 'integer',
        'category_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }



}
