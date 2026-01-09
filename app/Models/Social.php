<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
