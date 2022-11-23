<?php

namespace App\Exports;

use App\Models\documento;
use Maatwebsite\Excel\Concerns\FromCollection;

class DocumentoExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return documento::where('estado',1)->get();
    }
}
