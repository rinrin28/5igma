<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'category_id',
        'department_id',
        'avg_expectation',
        'avg_satisfaction',
        'expectation_gap',
    ];

    protected $casts = [
        'avg_expectation' => 'float',
        'avg_satisfaction' => 'float',
        'expectation_gap' => 'float',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
