<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class General extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Get a general setting by key
     *
     * @param string $key
     * @return mixed
     */
    public static function getValue($key)
    {
        return static::where('key', $key)->value('value');
    }
}
