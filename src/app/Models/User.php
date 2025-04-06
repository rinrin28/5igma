<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'client_company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function clientCompany()
    {
        return $this->belongsTo(ClientCompany::class);
    }

    public function expectations()
    {
        return $this->hasMany(Expectation::class, 'user_id');
    }

    public function satisfactions()
    {
        return $this->hasMany(Satisfaction::class, 'user_id');
    }

    public function sub_responses()
    {
        return $this->hasMany(SubResponse::class, 'user_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }
}
