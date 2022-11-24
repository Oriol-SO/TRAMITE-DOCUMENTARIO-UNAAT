<?php

namespace App\Exports;

use App\Models\documento;
use App\Models\seguimiento;
use App\Models\tiempo;
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

class DocSeguimientos implements FromCollection,WithTitle,WithHeadings,WithStyles,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;


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
        $sheet->getStyle('A3:E3')->getFill()
        ->setFillType(StyleFill::FILL_SOLID)
        ->getStartColor()->setARGB('ACB9CA');

        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A1:E1');
       // $sheet->getStyle('A1')->setValignment('center');
       $cell=seguimiento::all()->count();
       $sheet->getStyle('A1:E'.$cell+3)->ApplyFromArray($borderDashed);

    
    }
    public function title(): string
    {
        return 'TIEMPOS';
    }

    public function headings(): array
    {
        return [
            ['TIEMPOS DE SEGUIMIENTO DE LOS DOCUMENTOS'],
            ['FECHA: '.Carbon::now()],
            [
                'N°',
                'DOCUMENTO',
                'FECHA DE BUSQUEDA',
                'UNIDAD',
                'DURACION EN SEGUNDOS',
            ]

        ];
    }

    public function collection()
    {
        $num=1;
        $seguis= seguimiento::all()->map(function($d) use(&$num){
            return[
                'N°'=>$num++,
                'DOCUMENTO'=>$d->documento_id,
                'FECHA DE BUSQUEDA'=>$d->inicio,
                'UNIDAD'=>'pro',
                'DURACION EN SEGUNDOS'=>strtotime($d->final)-strtotime($d->inicio),
            ];
        });

        return $seguis;
    }
}
