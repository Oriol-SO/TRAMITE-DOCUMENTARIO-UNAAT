<?php

namespace App\Exports;

use App\Models\documento;
use Maatwebsite\Excel\Concerns\FromCollection;

class DocumentoFechasExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($fecha1,$fecha2,$tipo)
    {
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
        $this->tipo = $tipo;
    }
    public function collection()
    {
        return documento::where('estado',1)->whereBetween($this->tipo, [$this->fecha1, $this->fecha2])->get();
    }
}
