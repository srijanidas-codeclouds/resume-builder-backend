<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'resume_id',
        'skill_name',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
