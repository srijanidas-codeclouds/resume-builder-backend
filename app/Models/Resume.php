<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resume extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'professional_summary',
        'template',
        'accent_color',
        'is_public',
    ];

    // 🔗 Resume belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Resume sections
    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
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
    public function languages()
    {
        return $this->hasMany(Language::class);
    }
}