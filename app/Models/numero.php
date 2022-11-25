<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class numero extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero',
        'year',
        'unidad_id',
        'documento_id',
        'tipo',
    ];
}
