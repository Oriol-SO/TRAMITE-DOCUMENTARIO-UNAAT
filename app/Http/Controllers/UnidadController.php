<?php

namespace App\Http\Controllers;

use App\Models\documento;
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
            $docs_entrantes=Proceso::where('oficina_ouput',$oficina->id)->get('documento_id');

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
                    return[
                        'id'=>$d->id,
                        'documento'=>$d->documento,
                        'fecha'=>$d->fecha,
                        'path'=>$d->path,
                        'prioridad'=>$d->prioridad,
                        'remitente'=>$d->remitente,
                        'dni'=>$d->dni,
                        'estado'=>$d->estado,
                        'destino'=>$d->destino,
                        'tipo'=>$d->tipo,
                        'tiempo_final'=>$d->fecha_fin,
                        'atendido'=>$antendido,    
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
            proceso::create([
                'recepcion'=>Carbon::now(),
                'documento_id'=>$documento->id,
                'oficina_input'=>$this->oficina,     
                'estado_der'=>0,  
                'estado_rep'=>1, 
            ]);
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
            return response()->json(['message'=>'Error al archivar']);
        }
    }
}
