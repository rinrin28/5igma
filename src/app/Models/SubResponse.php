<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'subcategory_id',
        'user_id',
        'score',
        'is_submitted',
        'save_at',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
