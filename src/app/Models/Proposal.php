<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'proposal',
        'description',
        'target_score',
        'is_active',
        'department_id',
        'survey_id'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'proposal_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'proposal_id');
    }

    public function pulseSurveys()
    {
        return $this->hasMany(PulseSurvey::class);
    }
}
