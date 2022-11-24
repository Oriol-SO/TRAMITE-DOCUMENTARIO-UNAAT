<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documento extends Model
{
    use HasFactory;
    protected $fillable = [
        'documento',
        'fecha',
        'remitente',
        'dni',
        'destino',
        'tipo',
        'oficina_id',
        'estado',
        'prioridad',
        'path',
        'tipo_doc',
        'numero_doc',
        'direccion',
        'referencia',
        'anexo',
        'folio',
        'fecha_fin',
        'resuelto',
        'fecha_ate'
    ];
}
