<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{   
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'skills',
        'languages',
        'accent_color',
        'template',
    ];

    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
    ];

    protected $attributes = [
        'skills' => '[]',
        'languages' => '[]',
    ];

    // Resume belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Resume sections
    public function personalDetails()
    {
        return $this->hasOne(PersonalDetails::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }
    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }
    public function socials()
    {
        return $this->hasMany(Social::class);
    }
}