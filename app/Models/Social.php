<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Social extends Model
{
    use HasFactory,HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'linkedIn',
        'github',
        'portfolio',
        'twitter',
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
