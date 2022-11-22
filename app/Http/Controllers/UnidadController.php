<?php

namespace App\Http\Controllers;

use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\role;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    private $oficina;
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $user=$request->user();
        $role=role::findOrFail($user->rol_id);
        $this->oficina=$role->oficina_id;
    }

    public function fetch_docs($id){
        $oficina=oficina::findOrFail($id);
        try{
            $docs_entrantes=Proceso::where('oficina_ouput',$oficina->id)->get('documento_id');

            $documentos=documento::whereIn('id',$docs_entrantes)->get();
            return $documentos;
        }catch(Exception $e){
            response()->json(['message'=>'Error al obtener documentos'],405);
        }
        return documento::all();
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
}
