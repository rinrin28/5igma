<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'survey_types_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function surveyType()
    {
        return $this->belongsTo(SurveyTypes::class, 'survey_types_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function expectations()
    {
        return $this->hasMany(Expectation::class);
    }

    public function satisfactions()
    {
        return $this->hasMany(Satisfaction::class);
    }

    public function subResponses()
    {
        return $this->hasMany(SubResponse::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function pulseSurvey()
    {
        return $this->hasMany(PulseSurvey::class);
    }
}
