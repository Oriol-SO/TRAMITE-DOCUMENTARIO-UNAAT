<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    use HasFactory;
    protected $fillable = [
        'recepcion',
        'derivar',
        'oficina_input',
        'oficina_ouput',
        'documento_id',
        'observacion',
        'estado_der',
        'estado_rep',
        'recibido',
        'prohevido',
        'tipo',
        'numero',
        'num_corre'
    ];
}
