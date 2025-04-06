<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }

    public function satisfactions()
    {
        return $this->hasMany(Satisfaction::class, 'category_id');
    }

    public function expectations()
    {
        return $this->hasMany(Expectation::class, 'category_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'category_id');
    }
}
