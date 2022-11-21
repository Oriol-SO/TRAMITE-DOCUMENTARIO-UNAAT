<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class oficina extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'tipo',
       
    ];
}
