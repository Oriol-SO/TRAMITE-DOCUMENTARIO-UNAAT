<?php

namespace App\Http\Controllers;

use App\Models\documento;
use App\Models\numero;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\role;
use App\Models\tiempo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnidadController extends Controller
{
    private $oficina;
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $user=$request->user();
       // $role=role::findOrFail($user->rol_id);
        $this->oficina=$user->oficina_id;
    }

    public function fetch_docs($id){
        $oficina=oficina::findOrFail($id);
        try{
            $docs_entrantes=Proceso::where('oficina_ouput',$oficina->id )->orWhere('oficina_input',$oficina->id )->get('documento_id');

            $documentos=documento::whereIn('id',$docs_entrantes)->orderBy('prioridad', 'asc')->get()->map(function($d){
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
                        'id'=>$d->id,
                        'documento'=>$d->documento,
                        'fecha'=>$d->fecha,
                        'path'=>$d->path,
                        'prioridad'=>$d->prioridad,
                        'remitente'=>$d->remitente,
                        'dni'=>$d->dni,
                        'estado'=>$est,
                        'destino'=>$d->destino,
                        'tipo'=>$d->tipo,
                        'tiempo_final'=>$d->fecha_fin,
                        'atendido'=>$antendido,    
                        'numero_doc'=>$d->numero_doc,
                        'num_corre'=>$d->num_corre,
                        
                    ];
            });
            
            return $documentos;
        }catch(Exception $e){
            response()->json(['message'=>'Error al obtener documentos'],405);
        }
       // return documento::all();
    }

    public function recepcionar_doc(Request $request){
        $request->validate([
            'documento'=>'required|numeric',
            'proceso'=>'required|numeric',
        ]);
        $documento=documento::findOrFail($request->documento);
        $proceso=Proceso::findOrFail($request->proceso);
        try{
            if($proceso->documento_id!=$documento->id){
                return response()->json(['message'=>'los documentos no coinciden'],405);
            }
            if($proceso->estado_der!=1){
                return response()->json(['mesage'=>'este documento no fue derivado'],405);
            }
            if($proceso->oficina_ouput!=$this->oficina){
                return response()->json(['mesage'=>'no te corresponde derivar este documento'],405);
            }
            if($documento->oficina_id!=$this->oficina){
                return response()->json(['mesage'=>'no te corresponde derivar este documento .'],405);
            }
            //recivir proceso
            Proceso::where('id',$proceso->id)->update(['recibido'=>1]);
            //creamos el nuevo registro del documento
            $fecha=Carbon::now();
            $year = $fecha->year;
            $num=numero::where('unidad_id',$this->oficina)->whereYear('year',$year)->count();
            proceso::create([
                'recepcion'=>Carbon::now(),
                'documento_id'=>$documento->id,
                'oficina_input'=>$this->oficina,     
                'estado_der'=>0,  
                'estado_rep'=>1, 
                'num_corre'=>$num+1,
            ]);          
            numero::create([
                'numero'=>$num+1,
                'unidad_id'=>$this->oficina,
                'year'=>$fecha,
                'documento_id'=>$documento->id,
                'tipo'=>2,
            ]);
            return true;
           // documento::where('id')

        }catch(Exception $e){
            return response()->json(['messager'=>'Error al recepcionar documento'],405);
        }
    }

    public function cambiar_doc(Request $request,$id){
        $request->validate([
            'documento'=>'required|numeric',
            'archivo'=>'required'
        ]);
        $documento=documento::findOrFail($id);
        try{
            if($documento->id!=$request->id){
                return response()->json(['message'=>'los documentos no coinciden'],405);
            }
            if($documento->oficina_id!=$this->oficina){
                return response()->json(['message'=>'No puedes cambiar el documento'],405);
            }
            //subimos el documento
            $direccion='documentos';
            $newurl=Storage::url($request->file('archivo')->store($direccion,'public_file'));
            //eliminamos el anterior documento
            if($documento->path!=''){
                $path = public_path().$documento->path;
                unlink($path);
            }
            $documento->path=$newurl;
            $documento->save();
            return 'cambiado';

        }catch(Exception $e){
            return response()->json(['message'=>'Error al cambiar documento'],405);
        }
    }


    public function archivar_doc(Request $request){
        $request->validate([
            'documento'=>'required|numeric'
        ]);
        $documento=documento::findOrFail($request->documento);
        try{
           if($documento->estado==1){
            return response()->json(['message'=>'El documento ya finalizó'],405);
           }
           //actualizamos el documento
           $documento->estado=1;
           $documento->fecha_fin=Carbon::now();
           $documento->save();
           return true;
        }catch(Exception $e){
            return response()->json(['message'=>'Error al archivar'],405);
        }
    }

    public function resolver_doc(Request $request){
        $request->validate([
            'documento'=>'required|numeric'
        ]);
        $documento=documento::findOrFail($request->documento);
        try{
           if($documento->estado==1){
            return response()->json(['message'=>'El documento ya finalizó'],405);    
           }
           if($documento->resuelto==1){
            return response()->json(['message'=>'El documento ya fue antendido'],405);
           }
           //actualizamos el documento
           $documento->resuelto=1;
           $documento->fecha_ate=Carbon::now();
           $documento->fecha_fin=Carbon::now();
           $documento->save();
           return true;
        }catch(Exception $e){
            return response()->json(['message'=>'Error al anteder'],405);
        }
    }




    public function add_documento_unidad(Request $request){
        $request->validate([
            'nombre'=>'required',
            //'remitente'=>'required',
            //'dni'=>'required|numeric',
            //'archivo'=>'required',
            //'destino'=>'required',
            'tipo'=>'required',
            'prioridad'=>'required',
            'tipo_doc'=>'required',
            'numero_doc'=>'required',
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
                'oficina_id'=>$this->oficina,
                'tipo_doc'=>$request->tipo_doc,
                'numero_doc'=>$request->numero_doc,
                'direccion'=>$request->direccion,
                'referencia'=>$request->referencia,
                'anexo'=>$request->anexo,
                'folio'=>$request->folio,
                'provehido'=>$request->provehido,
            ]);
            $inicio = strtotime($request->tiempo_inicio);
            $final = strtotime($request->tiempo_fin);
            tiempo::create([
                'documento_id'=>$doc->id,
                'inicio'=>date('Y-m-d H:i:s',$inicio ),
                'final'=>date('Y-m-d H:i:s',$final ),
                'unidad_id'=>$this->oficina,
            ]);
            Proceso::create([
                'recepcion'=>Carbon::now(),
                'documento_id'=>$doc->id,
                'oficina_input'=>$this->oficina,     
                'estado_der'=>0,  
                'estado_rep'=>1,  
                'recibido'=>0,
            ]);
            $anio=Carbon::now();
            $year = $anio->year;
            $numer=numero::where('unidad_id',$this->oficina)->whereYear('year',$year)->count();
            numero::create([
                'numero'=>$numer+1,
                'unidad_id'=>$this->oficina,
                'year'=>$anio,
                'documento_id'=>$doc->id,
            ]);

            return true;
        }catch(Exception $e){
            return $e;
            return response()->json(['message'=>'Error al subir documento'],405);
        }
    }


    public function obtener_numero(){
        try{
            $fecha=Carbon::now();
            $year = $fecha->year;
            $numero=numero::where('unidad_id',$this->oficina)->whereYear('year',$year)->count();
            return $numero+1;
        }catch(Exception $e){
            return response()->json(['message'=>'No se puede obtener el numero correlativo'],405);
        }

    }
}
