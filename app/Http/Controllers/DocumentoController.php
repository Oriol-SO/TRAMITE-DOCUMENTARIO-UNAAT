<?php

namespace App\Http\Controllers;

use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\role;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        return documento::all();
    }

    public function add_documento(Request $request){
        $request->validate([
            'nombre'=>'required',
            'remitente'=>'required',
            'dni'=>'required|numeric',
            'archivo'=>'required',
            'destino'=>'required',
            'tipo'=>'required',
            'prioridad'=>'required',
        ]);
        try{
            $direccion='documentos';
            $url=Storage::url($request->file('archivo')->store($direccion,'public_file'));
            
            $doc=documento::create([
                'documento'=>$request->nombre,
                'fecha'=>Carbon::now(),
                'remitente'=>$request->remitente,
                'dni'=>$request->dni,
                'destino'=>$request->destino,
                'path'=>$url,
                'tipo'=>$request->tipo,
                'estado'=>'pendiente',
                'prioridad'=>$request->prioridad,
                'oficina_id'=>1,
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
            //return $e;
            return response()->json(['message'=>'Error al subir documento'],405);
        }
   
    }

    public function dato_doc($id){
        $d=documento::findOrFail($id);
        try{
            return [
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'path'=>$d->path,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'tipo'=>$d->tipo,
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p) use(&$d){
                    $oficina_i=oficina::where('id',$p->oficina_input)->first();
                    $oficina_o=oficina::where('id',$p->oficina_ouput)->first();
                   // $documento=documento::findOrFail($d)
                    $der=false;
                    $rep=false;
                    if($this->oficina==$p->oficina_input && $p->oficina_ouput==null){
                        $der=true;
                    }
                    if($this->oficina==$p->oficina_ouput  &&$p->recibido==0 && $d->oficina_id==$this->oficina){
                        $rep=true;
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
            Proceso::where('id',$proceso->id)->update(['derivar'=>Carbon::now(),'oficina_ouput'=>$oficina->id,'estado_der'=>1]);
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
}


/*
//codigo para borrar
$path = public_path().$file->path;
unlink($path);
*/