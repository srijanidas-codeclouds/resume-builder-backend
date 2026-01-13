<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certification extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'issuer',
        'issued_date',
        'url',
    ];

    protected $casts = [
        'issued_date'  => 'date',
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
