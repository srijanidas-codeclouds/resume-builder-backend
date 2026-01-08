<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certification extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'resume_id',
        'title',
        'issuer',
        'issue_date',
        'url',
    ];

    protected $casts = [
        'issue_date'  => 'date',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
