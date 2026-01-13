<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Education extends Model
{
    use HasFactory,HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'institution',
        'degree',
        'field',
        'grade',
        'start_date',
        'end_date',
    ];

    protected $casts = [
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
