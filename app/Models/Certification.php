<?php

namespace App\Models;

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

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
