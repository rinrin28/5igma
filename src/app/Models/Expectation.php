<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'category_id',
        'user_id',
        'score',
        'is_submitted',
        'save_at',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
}
