<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'name', 'calling', 'timezone', 'currency', 'language'])]
class Country extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $casts = [
        'currency' => 'array',
        'language' => 'array',
    ];

    public $timestamps = false; 
    public $incrementing = false;
}
