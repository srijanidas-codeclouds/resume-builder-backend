<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, HasUuids;
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'tech_stack',
        'live_link',
        'github_link',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
        ];
    }

    protected $attributes = ['tech_stack' => '[]'];

    protected static function booted()
    {
        static::creating(function ($resume) {
            if (! $resume->id) {
                $resume->id = (string) Str::uuid();
            }
        });
    }


    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}

