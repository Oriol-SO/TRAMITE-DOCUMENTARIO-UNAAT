<?php

namespace App\Http\Controllers;

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
        return oficina::all()->map(function($o){
            return[
                'id'=>$o->id,
                'nombre'=>$o->nombre,
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


    public function documentos_rep(){
        return documento::where('estado',1)->get()->map(function($d){
            $ofi=oficina::where('id',$d->oficina_id)->first();
            return[
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'path'=>$d->path,
                'tipo'=>$d->tipo,
                'estado'=>$d->estado,
                'prioridad'=>$d->prioridad,
                'oficina_id'=>1,
                'oficina'=>$ofi?$ofi->nombre:null,
                'duracion'=>Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)).' días',
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p){
                    $ofi=oficina::where('id',$p->oficina_input)->first();
                    $ofo=oficina::where('id',$p->oficina_ouput)->first();
                    return[
                        'id'=>$p->id,
                        'oficina_input_nom'=>$ofi?$ofi->nombre:null,
                        'oficina_ouput_nom'=>$ofo?$ofo->nombre:null,
                        'recepcion'=>$p->recepcion,
                        'derivacion'=>$p->derivar,
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

        return documento::where('estado',1)->whereBetween($tipo, [$request->fecha1, $request->fecha2])->get()->map(function($d){
            $ofi=oficina::where('id',$d->oficina_id)->first();
            return[
                'id'=>$d->id,
                'documento'=>$d->documento,
                'fecha'=>$d->fecha,
                'remitente'=>$d->remitente,
                'dni'=>$d->dni,
                'destino'=>$d->destino,
                'path'=>$d->path,
                'tipo'=>$d->tipo,
                'estado'=>$d->estado,
                'prioridad'=>$d->prioridad,
                'oficina_id'=>1,
                'oficina'=>$ofi?$ofi->nombre:null,
                'duracion'=>Carbon::parse($d->fecha)->diffInDays(Carbon::parse($d->fecha_fin)).' días',
                'proceso'=>Proceso::where('documento_id',$d->id)->get()->map(function($p){
                    $ofi=oficina::where('id',$p->oficina_input)->first();
                    $ofo=oficina::where('id',$p->oficina_ouput)->first();
                    return[
                        'id'=>$p->id,
                        'oficina_input_nom'=>$ofi?$ofi->nombre:null,
                        'oficina_ouput_nom'=>$ofo?$ofo->nombre:null,
                        'recepcion'=>$p->recepcion,
                        'derivacion'=>$p->derivar,
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
        ]);
        return true;
        
    }
}
