<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalDetails extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = true;

    protected $fillable = [
        'full_name',
        'designation',
        'email',
        'phone',
        'location',
    ];


public function resume()
{
    return $this->belongsTo(Resume::class);
}
}