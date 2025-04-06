<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    protected $fillable = [
        'question',
        'order'
    ];

    public function answers()
    {
        return $this->hasMany(FeedbackAnswer::class);
    }
} 