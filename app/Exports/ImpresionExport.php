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
        $sheet->getStyle('A3:L3')->getFill()
        ->setFillType(StyleFill::FILL_SOLID)
        ->getStartColor()->setARGB('ACB9CA');

        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A1:L1');

        $docs_entrantes=Proceso::where('oficina_ouput',$this->unidad)->orWhere('oficina_input',$this->unidad )->get('documento_id');
        $cell=documento::whereIn('id',$docs_entrantes)->orderBy('prioridad', 'asc')->count();
        $sheet->getStyle('A1:L'.$cell+3)->ApplyFromArray($borderDashed);
        
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
                'UNIDAD',
                //'PRIORIDAD',
                'UNIDAD DERIVADA',
                'FECHA DE DERIVACION',
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
        $docs_entrantes=[0];
        $docs_entrantes=Proceso::where('oficina_ouput',$this->unidad )->orWhere('oficina_input',$this->unidad )->get('documento_id');
        $seguis=documento::whereIn('id',$docs_entrantes)->orderBy('prioridad', 'asc')->get()->map(function($d) use(&$num){
           // $duracion=(Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)));
           $pro=Proceso::where('documento_id',$d->id)->orderBy('id','asc')->first();
           $ofi=oficina::where('id',$pro?$pro->oficina_input:'0')->first();
           $proc_ou=Proceso::where('documento_id',$d->id)->where('oficina_input',$this->unidad)->orderBy('id','asc')->first();
           $ofi_oup=oficina::where('id',$proc_ou?$proc_ou->oficina_ouput:'0')->first();
            return[
                'N°'=>$num++,
                'CODIGO'=>$d->id,
                'FECHA DE REGISTRO'=>$d->fecha,
                'DOCUMENTO'=>$d->tipo,
                'NUMERO DOCUMENTO'=>$d->numero_doc,
                'ASUNTO'=>$d->documento,
                'DOCUMENTO TIPO'=>$d->tipo_doc,
                'INTERESADO'=>$d->remitente,
                'UNIDAD'=>$ofi?$ofi->nombre:'',
                //'PRIORIDAD'=>$this->prioridad($d->prioridad),
                'UNIDAD DERIVADA'=>$ofi_oup?$ofi_oup->nombre:'',
                'FECHA DE DERIVACION'=>$proc_ou?$proc_ou->derivar:'',
                'FECHA DE CULMINACION'=>$d->fecha_fin,
                //'DURACION DIAS'=>$duracion?$duracion:'0',
            ];
        });

        return $seguis;
    }
}
