<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'survey_id',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
