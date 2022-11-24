<?php

namespace App\Exports;

use App\Models\documento;
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

class DocExportsSeguimiento implements FromCollection,WithTitle,WithHeadings,WithStyles,ShouldAutoSize
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
        $sheet->getStyle('A4:C4')->getFill()
        ->setFillType(StyleFill::FILL_SOLID)
        ->getStartColor()->setARGB('ACB9CA');

        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A3:C3');
       // $sheet->getStyle('A1')->setValignment('center');
       $cell=seguimiento::where('documento_id',$this->documento)->count();
       $sheet->getStyle('A1:C'.$cell+4)->ApplyFromArray($borderDashed);

    
    }
    public function title(): string
    {
        return 'DOCUMENTO => '.$this->documento;
    }

    public function headings(): array
    {
        return [
            ['TIEMPO EN SEGUNDOS DE SEGUIMIENTO'],
            ['FECHA: '.Carbon::now()],
            ['DOCUMENTO:'.$this->documento],
            [
                'N°',
                'FECHA DE SEGUIMIENTO',
                'DURACION EN SEGUNDOS',
            ]

        ];
    }

    public function collection()
    {
        $num=1;
        $seguis= seguimiento::where('documento_id',$this->documento)->get()->map(function($d) use(&$num){
            return[
                'N°'=>$num++,
                'FECHA DE SEGUIMIENTO'=>$d->inicio,
                'DURACION EN SEGUNDOS'=>strtotime($d->final)-strtotime($d->inicio),
            ];
        });

        return $seguis;
    }
}
