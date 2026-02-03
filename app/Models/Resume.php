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
    'title',
    'summary', 
    'template',
    'accent_color',
    'status',
    'personal_details',
    'skills',
    'languages',
    'experiences',
    'projects',
    'education',
    'certifications',
    'socials',
];


    protected $casts = [
        'skills' => 'array',
        'languages' => 'array',
    ];

    protected $attributes = [
        'skills' => '[]',
        'languages' => '[]',
        'status' => 'draft',
    ];
    
    protected static function booted()
    {
        static::creating(function ($resume) {
            if (! $resume->id) {
                $resume->id = (string) Str::uuid();
            }
        });
    }

    public function getCompletionAttribute(): int
{
    $score = 0;

    // Core
    if (!empty($this->title)) {
        $score += 5;
    }

    if (!empty($this->summary)) {
        $score += 10;
    }

    // Personal Details (at least name + email)
    if (
        $this->personalDetails &&
        !empty($this->personalDetails->full_name) &&
        !empty($this->personalDetails->email)
    ) {
        $score += 15;
    }

    // Skills (at least 1)
    if (is_array($this->skills) && count($this->skills) > 0) {
        $score += 15;
    }

    // Languages (must have name)
    if (
        is_array($this->languages) &&
        collect($this->languages)->whereNotNull('name')->count() > 0
    ) {
        $score += 5;
    }

    // Experience
    if ($this->experiences()->exists()) {
        $score += 20;
    }

    // Education
    if ($this->education()->exists()) {
        $score += 15;
    }

    // Projects
    if ($this->projects()->exists()) {
        $score += 10;
    }

    // Certifications
    if ($this->certifications()->exists()) {
        $score += 5;
    }

    return min(100, $score);
}


// Resume.php
protected $appends = ['completion'];



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
    public function atsScores()
{
    return $this->hasMany(AtsScore::class);
}

}