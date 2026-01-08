<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'resume_id',
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

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}

