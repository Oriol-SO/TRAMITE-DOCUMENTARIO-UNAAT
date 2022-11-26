<?php

namespace App\Http\Controllers;

use App\Exports\DocExportSeguimientoOficina;
use App\Exports\DocExportsSeguimiento;
use App\Exports\DocExportTiempos;
use App\Exports\DocSeguimientos;
use App\Exports\DocumentoExport;
use App\Exports\DocumentoFechasExport;
use App\Models\documento;
use App\Models\oficina;
use App\Models\Proceso;
use App\Models\role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function exportar_seguimientos(Request $request){
        return Excel::download(new DocSeguimientos, 'documentos.xlsx');
    }
    public function exportar_docs_tiempos(Request $request){
        return Excel::download(new DocExportTiempos, 'documentos.xlsx');
    }

    public function exportar_docs_seguimiento_ofic(Request $request){
        return Excel::download(new DocExportSeguimientoOficina($request->documento), 'documentos.xlsx');
    }
    public function exportar_docs_seguimiento(Request $request){
        return Excel::download(new DocExportsSeguimiento($request->documento), 'documentos.xlsx');
    }

    public function exportar_docs(Request $request){
        $tipo='fecha';
        if($request->tipo=='Fecha de creación'){
            $tipo='fecha';
        }else if($request->tipo=='Fecha archivado'){
            $tipo='fecha_fin';
        }
        try{
            if($request->fecha1=='' || $request->fecha2==''){
                return Excel::download(new DocumentoExport, 'documentos.xlsx');
            }else{
                return Excel::download(new DocumentoFechasExport($request->fecha1,$request->fecha2,$tipo),"documento.xlsx");
            }
        }catch(Exception $e){
            return $e;
        }
        

    }



    public function getusers(){
        return User::all()->map(function($u){
            $rol=role::where('id',$u->rol_id)->first();
            $oficina=oficina::where('id',$u->oficina_id)->first();
            return[
                'id'=>$u->id,
                'nombre'=>$u->name,
                'email'=>$u->email,
                'rol_id'=>$u->rol,
                'dni'=>$u->dni,
                'rol'=>$rol?$rol->nombre:null,
                'oficina'=>$oficina?$oficina->nombre:null,
            ];
        });
    }

    public function roles(){
        return role::all()->map(function($r){
            return[
                'id'=>$r->id,
                'nombre'=>$r->nombre,
            ];
        });
    }

    public function oficinas(){
        return oficina::where('estado',1)->get()->map(function($o){
            return[
                'id'=>$o->id,
                'nombre'=>$o->nombre,
                'estado'=>$o->estado,
            ];
        });
    }

    protected function add_user(Request $request){
        $request->validate([
            'nombre'=>'required',
            'correo'=>'required|email',
            'dni'=>'required|numeric',
            'rol'=>'required',
            'oficina'=>'required'
        ]);
        try{
            $oficina=$request->oficina['id'];
            if($request->rol['id']==1){
                $oficina=9;
            }
            if($request->rol['id']==2){
                $oficina=1;
            }
            User::create([
                'name'=>$request->nombre,
                'email'=>$request->correo,
                'dni'=>$request->dni,
                'email_verified_at' =>now(),
                'password' => Hash::make($request->dni),
                'rol_id'=>$request->rol['id'],
                'oficina_id'=>$oficina
            ]);
        }catch(Exception $e){
            //return $e;
            
            return response()->json(['message'=>'Error al agregar usuario'],405);
        }
    }

    protected function editar_user(Request $request ,$id){
        $request->validate([
            'nombre'=>'required',
            'correo'=>'required|email',
            'dni'=>'required|numeric',
            
        ]);
       $user= User::findOrFail($id);
        try{
            $correos=User::where('email',$request->correo)->count();
            if($request->correo!=$user->email){
                $correos=User::where('email',$request->correo)->first();
                if($correos){
                    return response()->json(['message'=>'El correo ya fue tomado'],405);
                }
                User::where('id',$id)->update([
                    'name'=>$request->nombre,
                    'email'=>$request->correo,
                    'password' => Hash::make($request->dni),
                    'dni'=>$request->dni,
                    'email_verified_at' =>now(),
                ]);
            }else{
                User::where('id',$id)->update([
                    'name'=>$request->nombre,
                    'dni'=>$request->dni,
                    'email_verified_at' =>now(),
                    'password' => Hash::make($request->dni),
                ]);
            }
            
        }catch(Exception $e){
            return $e;
            return response()->json(['message'=>'Error al editar usuario'],405);
        }
        
        return $request;
    }

    public function prioridad($prioridad){
        switch($prioridad){
            case 20:
                return 'Normal';
            case 19:
                return 'Especial';
            case 18:
                return 'Urgente';
            case 17:
                return 'Muy urgente';
            default:
                return '';
        }
    }
    public function documentos_rep(){
        return documento::all()->map(function($d){
            $ofi=oficina::where('id',$d->oficina_id)->first();
            $est=3;
            if($d->resuelto==1){
                $est=2; 
            }
            if($d->estado==1){
                $est=1;
            }
            $count=1;
            return[
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'fecha_fin'=>$d->fecha_fin,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'path'=>$d->path,
                'tipo'=>$d->tipo,
                'tipo_doc'=>$d->tipo_doc,
                'numero'=>$d->numero_doc,
                'numero_doc'=>$d->numero_doc,
                'tiempo_final'=>$d->fecha_fin,
                'estado'=>$est,
                'estado_fin'=>$d->estado,
                'prioridad'=>$d->prioridad,
                'nombre_prioridad'=>$this->prioridad($d->prioridad),
                'oficina_id'=>1,
                'oficina'=>$ofi?$ofi->nombre:null,
                'duracion'=>Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)).' días',
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p) use(&$count){
                    $ofi=oficina::where('id',$p->oficina_input)->first();
                    $ofo=oficina::where('id',$p->oficina_ouput)->first();
                    //$procs=Proceso::where('id',$p->documento_id)->count();
                    $num=$count++;
                    $del=false;
                    if( $p->derivar!=null && $p->recibido!=1 && $p->estado_der==1 ){
                        $del=true;
                    }
                    return[
                        'num'=>$count++,
                        'documento_id'=>$p->documento_id,
                        'id'=>$p->id,
                        'nom_input'=>$ofi?$ofi->nombre:null,
                        'nom_ouput'=>$ofo?$ofo->nombre:null,
                        'recepcion'=>$p->recepcion,
                        'derivar'=>$p->derivar,
                        'prohevido'=>$p->prohevido,
                        'asunto'=>$p->asunto,
                        'tipo'=>$p->tipo,
                        'numero'=>$p->numero,
                        'eliminar'=>$del,
                        'estado_rep'=>$p->recibido,
                        'estado_der'=>$p->estado_der,
                    ];
                }),
            ];
        });
    }


    public function documentos_archivo(){
        return documento::all()->map(function($d){
            $ofi=oficina::where('id',$d->oficina_id)->first();
            $count=1;
            return[
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'fecha_fin'=>$d->fecha_fin,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'path'=>$d->path,
                'tipo'=>$d->tipo,
                'tiempo_final'=>$d->fecha_fin,
                'estado'=>$d->estado,
                'estado_res'=>$d->resuelto,
                'prioridad'=>$d->prioridad,
                'nombre_prioridad'=>$this->prioridad($d->prioridad),
                'oficina_id'=>1,
                'oficina'=>$ofi?$ofi->nombre:null,
                'duracion'=>Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)).' días',
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p)use(&$count){
                    $ofi=oficina::where('id',$p->oficina_input)->first();
                    $ofo=oficina::where('id',$p->oficina_ouput)->first();
                    //$procs=Proceso::where('id',$p->documento_id)->count();
                    $num=$count++;
                    $del=false;
                    if( $p->derivar!=null && $p->recibido!=1 && $p->estado_der==1 ){
                        $del=true;
                    }
                    return[
                        'num'=>$count++,
                        'documento_id'=>$p->documento_id,
                        'id'=>$p->id,
                        'nom_input'=>$ofi?$ofi->nombre:null,
                        'nom_ouput'=>$ofo?$ofo->nombre:null,
                        'recepcion'=>$p->recepcion,
                        'derivar'=>$p->derivar,
                        'prohevido'=>$p->prohevido,
                        'eliminar'=>$del,
                        'estado_rep'=>$p->recibido,
                        'estado_der'=>$p->estado_der,
                    ];
                }),
            ];
        });
    }



    public function buscar_fechas(Request $request){
        $tipo='fecha';
        if($request->tipo=='Fecha de creación'){
            $tipo='fecha';
        }else if($request->tipo=='Fecha archivado'){
            $tipo='fecha_fin';
        }

        return documento::whereBetween($tipo, [$request->fecha1, $request->fecha2])->get()->map(function($d){
            $ofi=oficina::where('id',$d->oficina_id)->first();
            $est=3;
            if($d->resuelto==1){
                $est=2; 
            }
            if($d->estado==1){
                $est=1;
            }
            $count=1;
            return[
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'fecha_fin'=>$d->fecha_fin,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'path'=>$d->path,
                'tipo'=>$d->tipo,
                'tipo_doc'=>$d->tipo_doc,
                'numero'=>$d->numero_doc,
                'numero_doc'=>$d->numero_doc,
                'tiempo_final'=>$d->fecha_fin,
                'estado'=>$est,
                'estado_fin'=>$d->estado,
                'prioridad'=>$d->prioridad,
                'nombre_prioridad'=>$this->prioridad($d->prioridad),
                'oficina_id'=>1,
                'oficina'=>$ofi?$ofi->nombre:null,
                'duracion'=>Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)).' días',
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p) use(&$count){
                    $ofi=oficina::where('id',$p->oficina_input)->first();
                    $ofo=oficina::where('id',$p->oficina_ouput)->first();
                    //$procs=Proceso::where('id',$p->documento_id)->count();
                    $num=$count++;
                    $del=false;
                    if( $p->derivar!=null && $p->recibido!=1 && $p->estado_der==1 ){
                        $del=true;
                    }
                    return[
                        'num'=>$count++,
                        'documento_id'=>$p->documento_id,
                        'id'=>$p->id,
                        'nom_input'=>$ofi?$ofi->nombre:null,
                        'nom_ouput'=>$ofo?$ofo->nombre:null,
                        'recepcion'=>$p->recepcion,
                        'derivar'=>$p->derivar,
                        'prohevido'=>$p->prohevido,
                        'asunto'=>$p->asunto,
                        'tipo'=>$p->tipo,
                        'numero'=>$p->numero,
                        'eliminar'=>$del,
                        'estado_rep'=>$p->recibido,
                        'estado_der'=>$p->estado_der,
                    ];
                }),
            ];
        });

        
    }

    public function add_oficina(Request $request){
        $request->validate([
            'nombre'=>'required',
        ]);

        $ofi=oficina::where('nombre',$request->nombre)->first();
        if($ofi){
            return response()->json(['message'=>'Y a existe una oficina con el mismo nombre'],405);
        }
        oficina::create([
            'nombre'=>$request->nombre,
            'estado'=>1,
        ]);
        return true;
        
    }

    public function estado_oficina($id,$estado){
        $ofi=oficina::findOrFail($id);
        $ofi->estado=!$estado;
        $ofi->save();
    }

    public function eliminar_derivacion(Request $request){
        $request->validate([
            'proceso'=>'required|numeric',
            'documento'=>'required|numeric',
        ]);

        $documento=documento::findOrFail($request->documento);
        $proceso=Proceso::findOrFail($request->proceso);
        try{

            if($documento->id!=$proceso->documento_id){
                return response()->json(['message'=>'los documentos no coinciden'],405);
            }
            if($documento->estado==1){
                return response()->json(['message'=>'El documento ya finalizó'],405);
            }
            if($documento->resuelto==1){
                return response()->json(['message'=>'El documento ya fue atendido'],405);
            }
            if($proceso->oficina_input==null || $proceso->oficina_ouput==null){
                return response()->json(['message'=>'No puedes eliminar .'],405);
            }
            if($proceso->derivar==1 && $proceso->recibido!=1 && $proceso->estado_der==1){
                return response()->json(['message'=>'No puedes eliminar ...'],405);
            }
            //eliminamos la derivacion
            $proceso->oficina_ouput=null;
            $proceso->derivar=null;
            $proceso->estado_der=0;
            $proceso->tipo=null;
            $proceso->numero=null;
            $proceso->prohevido=null;
            $proceso->save();
            return 'eliminado';

        }catch(Exception $e){
            return $e;
            return response()->json(['message'=>'Error al eliminar proceso'],405);
        }
    }
}
