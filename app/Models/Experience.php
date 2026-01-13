<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experience extends Model
{
    use HasFactory,HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'organization',
        'position',
        'start_date',
        'end_date',
        'description',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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

