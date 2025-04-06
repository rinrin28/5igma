<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_info',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'client_company_id');
    }
}
