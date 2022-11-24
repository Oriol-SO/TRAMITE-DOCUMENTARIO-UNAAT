<?php

namespace App\Http\Controllers;

use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\role;
use App\Models\seguimiento;
use App\Models\tiempo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Break_;

class DocumentoController extends Controller
{

    private $oficina;
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $user=$request->user();
       // $role=role::findOrFail($user->rol_id);
        $this->oficina=$user->oficina_id;
    }

    public function documentos(){
        $num=1;
        return documento::where('id','<>',null)->orderBy('id', 'desc')->get()->map(function($d)use(&$num){
            $proceso=Proceso::where('documento_id',$d->id)->orderBy('id', 'desc')->first();
                $der=false;
                $rep=false;
                $archi=false;
                if($d->estado==0 && $this->oficina==$proceso->oficina_input && $proceso->oficina_ouput==null){
                    $der=true;
                }
                if($d->estado==0 && $this->oficina==$proceso->oficina_ouput  && $proceso->recibido==0 && $d->oficina_id==$this->oficina){
                    $rep=true;
                }
                if($d->estado==0 && $this->oficina==$proceso->oficina_input && $proceso->oficina_ouput==null){
                    $archi=true;
                }
                $antendido=true;
                if($der==true || $rep ==true ){
                    $antendido=false;
                }
                if($archi==true){
                    $antendido=false;
                }
                $est=3;
                    if($d->resuelto==1){
                        $est=2; 
                    }
                    if($d->estado==1){
                        $est=1;
                    }
            return[
                'n'=>$num++,
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'path'=>$d->path,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'estado'=>$est,
                'prioridad'=>$d->prioridad,
                'destino'=>$d->destino,
                'tipo'=>$d->tipo,
                'tiempo_final'=>$d->fecha_fin,
                'atendido'=>$antendido,
                'archivado'=>1,
               // 'derivar'=>$der,     
            ];
        });
    }

    public function add_documento(Request $request){
        
        $request->validate([
            'nombre'=>'required',
            'remitente'=>'required',
            'dni'=>'required|numeric',
            //'archivo'=>'required',
            //'destino'=>'required',
            'tipo'=>'required',
            'prioridad'=>'required',
            'tipo_doc'=>'required',
            //'numero_doc'=>'required',
        ]);
        try{
            
            $prioridad=20;
            switch($request->prioridad){
                case 'NORMAL':
                    $prioridad=20;
                    break;
                case 'ESPECIAL':
                    $prioridad=19;
                    break;
                case 'URGENTE':
                    $prioridad=18;
                    break;
                case 'MUY URGENTE':
                    $prioridad=17;
                    break;
                default :
                    $prioridad=20;
                    break;
            }

            $url='';
            if($request->archivo){
                $direccion='documentos';
                $url=Storage::url($request->file('archivo')->store($direccion,'public_file'));
            }

            $doc=documento::create([
                'documento'=>$request->nombre,
                'fecha'=>Carbon::now(),
                'remitente'=>$request->remitente,
                'dni'=>$request->dni,
                //'destino'=>$request->destino,
                'path'=>$url,
                'tipo'=>$request->tipo,
                'estado'=>0,
                'prioridad'=>$prioridad,
                'oficina_id'=>1,
                'tipo_doc'=>$request->tipo_doc,
                //'numero_doc'=>$request->numero_doc,
                'direccion'=>$request->direccion,
                'referencia'=>$request->referencia,
                'anexo'=>$request->anexo,
                'folio'=>$request->folio,
            ]);
            $inicio = strtotime($request->tiempo_inicio);
            $final = strtotime($request->tiempo_fin);
            tiempo::create([
                'documento_id'=>$doc->id,
                'inicio'=>date('Y-m-d H:i:s',$inicio ),
                'final'=>date('Y-m-d H:i:s',$final ),
            ]);
            Proceso::create([
                'recepcion'=>Carbon::now(),
                'documento_id'=>$doc->id,
                'oficina_input'=>1,     
                'estado_der'=>0,  
                'estado_rep'=>1,  
                'recibido'=>0,
            ]);

            return true;
        }catch(Exception $e){
            return $e;
            return response()->json(['message'=>'Error al subir documento'],405);
        }
   
    }

    public function tiempo($inicio, $fin){
        $tiempo='-';
        $h1=date("H",strtotime($inicio));
        $m1=date("i",strtotime($inicio));
        $s1=date("s",strtotime($inicio));
        $h2=date("H",strtotime($fin));
        $m2=date("i",strtotime($fin));
        $s2=date("s",strtotime($fin));
        if($h1==$h2){
            if($m1==$m2){
                $tiempo=($s2-$s1).' s';
            }else if($m1+1==$m2){
                $tiempo=(60-$s1)+$s2.' s';
            }else{
                $tiempo=(($m2-$m1)*60)+$s1+$s2.' s';
            }
        }
        return $tiempo;
    }
    public function dato_doc($id){
        $d=documento::findOrFail($id);
        $tiempo=tiempo::where('documento_id',$d->id)->first();
        $date=$this->tiempo($tiempo->inicio,$tiempo->final);
        $pro=Proceso::where('documento_id',$d->id)->orderBy('id','desc')->first();
       // $oficina_i=oficina::where('id',$pro->oficina_input)->first();
        //$oficina_o=oficina::where('id',$pro->oficina_ouput)->first();
        $der=false;
        if($d->estado==0 && $this->oficina==$pro->oficina_input && $pro->oficina_ouput==null){
            $der=true;
        }

        try{
            return [
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'path'=>$d->path,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'estado'=>$d->estado,
                'destino'=>$d->destino,
                'tipo'=>$d->tipo,
                'resuelto'=>$d->resuelto,
                'tiempo_creacion'=>$date,
                'tiempo_final'=>$d->fecha_fin,
                'accion'=>$der,
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p) use(&$d){
                    $oficina_i=oficina::where('id',$p->oficina_input)->first();
                    $oficina_o=oficina::where('id',$p->oficina_ouput)->first();
                   // $documento=documento::findOrFail($d)
                    $der=false;
                    $rep=false;
                    $archi=false;
                    if($d->estado==0 && $d->resuelto==0 && $this->oficina==$p->oficina_input && $p->oficina_ouput==null){
                        $der=true;
                    }
                    if($d->estado==0 && $d->resuelto==0 && $this->oficina==$p->oficina_ouput  &&$p->recibido==0 && $d->oficina_id==$this->oficina){
                        $rep=true;
                    }
                    if($d->estado==0 && $this->oficina==$p->oficina_input && $p->oficina_ouput==null){
                        $archi=true;
                    }
                    return[
                        'id'=>$p->id,
                        'documento'=>$p->documento_id,
                        'recepcion'=>$p->recepcion,
                        'derivar'=>$p->derivar,
                        'oficina_input'=>$p->oficina_input,
                        'oficina_ouput'=>$p->oficina_ouput,
                        'nom_input'=>$oficina_i?$oficina_i->nombre:null,
                        'nom_ouput'=>$oficina_o?$oficina_o->nombre:null,
                        'ac_derivar'=>$der,
                        'ac_rep'=>$rep,
                        'archivar'=>$archi,
                        'prohevido'=>$p->prohevido,
                    ];
                }),
            ];
        }catch(Exception $e){
            return $e;
            return response()->json(['message'=>'Error al obtener datos'],405);
        }
    }

    public function derrivar_doc(Request $request){
        $request->validate([
            'oficina'=>'required',
            'documento'=>'required',
            'proceso'=>'required|numeric',
        ]);
        $documento=documento::findOrFail($request->documento);
        $oficina=oficina::findOrFail($request->oficina['id']);
        $proceso=Proceso::findOrFail($request->proceso);
        try{
            //verificamos que el proceso no tenga derivacion
            if($proceso->oficina_ouput!=null || $proceso->oficina_ouput!=''){
                return response()->json(['message'=>'El proceso ya fué derivado'],405);
            }
            if($proceso->oficina_input==$oficina->id){
                return response()->json(['message'=>'No puedes derivar a la misma oficina'],405);
            }

            // ahora si derivamos a la oficina
            Proceso::where('id',$proceso->id)->update([
                'derivar'=>Carbon::now(),
                'oficina_ouput'=>$oficina->id,
                'estado_der'=>1,
                'prohevido'=>$request->prohevido,
            ]);
            documento::where('id',$documento->id)->update(['oficina_id'=>$oficina->id]);
            //ahora creamos un nuevo registro 
            /*Proceso::create([
                'recepcion'=>Carbon::now(),
                'documento_id'=>$documento->id,
                'oficina_input'=>$oficina->id,   
            ]);*/
            return 'derivado';
        }catch(Exception $e){
            //return $e;
            return response()->json(['message'=>'Error al derivar'],405);
        }
        
        
    }


    public function agregar_tiempo_doc(Request $request,$id){
        if($request->inicio=='0' || $request->fin=='0'){
            return false;
        }
        $documento=documento::findOrFail($id);

        $inicio = strtotime($request->inicio);
        $final = strtotime($request->fin);
        seguimiento::create([
            'documento_id'=>$documento->id,
            'inicio'=>date('Y-m-d H:i:s',$inicio ),
            'final'=>date('Y-m-d H:i:s',$final ),
        ]);
    }
}


/*
//codigo para borrar
$path = public_path().$file->path;
unlink($path);
*/