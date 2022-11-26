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

class DocumentoFechasExport implements FromCollection,WithTitle,WithHeadings,WithStyles,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    public function __construct($fecha1,$fecha2,$tipo)
    {
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
        $this->tipo = $tipo;
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
       // $sheet->getStyle('A1')->setValignment('center');
       $cell=documento::whereBetween($this->tipo, [$this->fecha1, $this->fecha2])->count();
       $sheet->getStyle('A1:J'.$cell+3)->ApplyFromArray($borderDashed);

    
    }
    public function title(): string
    {
        return 'DOCUMENTOS';
    }

    public function headings(): array
    {
        return [
            ['LISTA DE DOCUMENTOS REGISTRADOS DESDE'.$this->fecha1.' A '.$this->fecha2],
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
        $seguis=documento::whereBetween($this->tipo, [$this->fecha1, $this->fecha2])->get()->map(function($d) use(&$num){
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
