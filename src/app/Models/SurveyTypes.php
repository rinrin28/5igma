<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'survey_types_id');
    }
}
