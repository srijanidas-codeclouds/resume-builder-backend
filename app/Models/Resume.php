<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    
    protected static function booted()
    {
        static::creating(function ($resume) {
            if (! $resume->id) {
                $resume->id = (string) Str::uuid();
            }
        });
    }

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
        return $this->hasOne(Social::class);
    }
}