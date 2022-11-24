<?php

namespace App\Exports;

use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\seguimiento;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Fill as StyleFill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocExportSeguimientoOficina implements FromCollection,WithTitle,WithHeadings,WithStyles,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function __construct($documento)
    {
        $this->documento = $documento;
    }

    public function styles(Worksheet $sheet)
    {
        $borderDashed = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A5:F5')->getFill()
        ->setFillType(StyleFill::FILL_SOLID)
        ->getStartColor()->setARGB('ACB9CA');

        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');
       // $sheet->getStyle('A1')->setValignment('center');
       $cell=Proceso::where('documento_id',$this->documento)->count();
       $sheet->getStyle('A1:F'.$cell+5)->ApplyFromArray($borderDashed);

    
    }
    public function title(): string
    {
        return 'DOCUMENTO => '.$this->documento;
    }

    public function headings(): array
    {
        $doc=documento::findOrFail($this->documento);
        return [
            ['SEGUIMIENTO DE DOCUMENTO' ],
            ['FECHA CREACION:'. $doc->fecha],
            ['FECHA FINAL:'. $doc->fecha_fin],
            ['DOCUMENTO:'.$this->documento],
            [
                'N°',
                'FECHA DE RECEPCION',
                'UNIDAD DE RECEPCION',
                'FECHA DE DERIVACION',
                'UNIDAD DE DERIVACION',
                'DURACION EN DÍAS',
            ]

        ];
    }

    public function collection()
    {
        $num=1;
        $seguis= Proceso::where('documento_id',$this->documento)->get()->map(function($d) use(&$num){
            $ofi_i=oficina::where('id',$d->oficina_input)->first();
            $ofi_o=oficina::where('id',$d->oficina_ouput)->first();
            $duracion=(Carbon::parse($d->recepcion)->diffInDays(Carbon::parse($d->derivar)));
            return[
                'N°'=>$num++,
                'FECHA DE RECEPCION'=>$d->recepcion,
                'UNIDAD DE RECEPCION'=>$ofi_i?$ofi_i->nombre:null,
                'FECHA DE DERIVACION'=>$d->derivar,
                'UNIDAD DE DERIVACION'=>$ofi_o?$ofi_o->nombre:null,
                'DURACION EN DÍAS'=>$duracion?$duracion:'0',
            ];
        });

        return $seguis;
    }
}
