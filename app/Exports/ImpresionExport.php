<?php

namespace App\Exports;

use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
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

class ImpresionExport implements FromCollection,WithTitle,WithHeadings,WithStyles,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function __construct($unidad)
    {
        $this->unidad = $unidad;
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
        $sheet->getStyle('A3:J3')->getFill()
        ->setFillType(StyleFill::FILL_SOLID)
        ->getStartColor()->setARGB('ACB9CA');

        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A1:J1');
        if($this->unidad==1){
            $cell=documento::where('id','<>',null)->count();
            $sheet->getStyle('A1:J'.$cell+3)->ApplyFromArray($borderDashed);
        }else{
            $docs_entrantes=Proceso::where('oficina_ouput',$this->unidad)->orWhere('oficina_input',$this->unidad )->get('documento_id');
            $cell=documento::whereIn('id',$docs_entrantes)->orderBy('prioridad', 'asc')->count();
            $sheet->getStyle('A1:J'.$cell+3)->ApplyFromArray($borderDashed);
        }
       // $sheet->getStyle('A1')->setValignment('center');
      // $cell=documento::where('estado',)->count();
      

    
    }
    public function title(): string
    {
        return 'DOCUMENTOS';
    }

    public function headings(): array
    {
        $ofi=oficina::where('id',$this->unidad)->first();
        return [
            ['LISTA DE DOCUMENTOS TRATADOS EN '.($ofi?strtoupper($ofi->nombre):'')],
            ['FECHA DE REPORTE: '.Carbon::now()],
            [
                'N°',
                'CODIGO',
                'FECHA DE REGISTRO',
                'DOCUMENTO',
                'NUMERO DOCUMENTO',
                'ASUNTO',
                'DOCUMENTO TIPO',
                'INTERESADO',
                'PRIORIDAD',
                'FECHA DE CULMINACION',
               // 'DURACION DIAS',
            ]

        ];
    }
    public function prioridad($prioridad){
        switch($prioridad){
            case 20:
                return 'NORMAL';
            case 19:
                return 'ESPECIAL';
            case 18:
                return 'URGENTE';
            case 17:
                return 'MUY URGENTE';
            default:
                return '';
        }
    }
    public function collection()
    {
        $num=1;
        $docs_entrantes=[1];
        if($this->unidad==1){
            $docs_entrantes=documento::where('id','<>',null)->get('id');
        }else{
            $docs_entrantes=Proceso::where('oficina_ouput',$this->unidad )->orWhere('oficina_input',$this->unidad )->get('documento_id');
        }
        $seguis=documento::whereIn('id',$docs_entrantes)->orderBy('prioridad', 'asc')->get()->map(function($d) use(&$num){
           // $duracion=(Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)));
            return[
                'N°'=>$num++,
                'CODIGO'=>$d->id,
                'FECHA DE REGISTRO'=>$d->fecha,
                'DOCUMENTO'=>$d->tipo,
                'NUMERO DOCUMENTO'=>$d->numero_doc,
                'ASUNTO'=>$d->documento,
                'DOCUMENTO TIPO'=>$d->tipo_doc,
                'INTERESADO'=>$d->remitente,
                'PRIORIDAD'=>$this->prioridad($d->prioridad),
                'FECHA DE CULMINACION'=>$d->fecha_fin,
                //'DURACION DIAS'=>$duracion?$duracion:'0',
            ];
        });

        return $seguis;
    }
}
