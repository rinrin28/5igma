<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortTermGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'pulse_survey_id',
        'target_score',
    ];

    public function pulseSurvey()
    {
        return $this->belongsTo(PulseSurvey::class);
    }
}
