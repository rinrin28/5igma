<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'survey_id', 'feedback_question_id', 'answer','is_completed'];

    protected $casts = [
        'answer' => 'integer',
    ];

    public static function rules()
    {
        return [
            'answer' => 'required|integer|min:1|max:5',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function question()
    {
        return $this->belongsTo(FeedbackQuestion::class, 'feedback_question_id');
    }
}
