<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoryScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'subcategory_id',
        'department_id',
        'avg_score'
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
