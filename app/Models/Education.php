<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Education extends Model
{
    use HasFactory;

    protected $table = 'education';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'resume_id',
        'institution',
        'degree',
        'field',
        'graduation_date',
        'gpa',
    ];

    protected $casts = [
        'graduation_date' => 'date',
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
