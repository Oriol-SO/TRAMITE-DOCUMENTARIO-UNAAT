<?php

namespace App\Http\Controllers;

use App\Models\oficina;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class OficinaController extends Controller
{
    public function fetch_oficinas(){
        return oficina::where('estado',1)->get();
    }

    public function oficinas(){
        return oficina::all();
    }


    public function CambiarEstado(Request $request){
        $request->validate([
            'oficina'=>'required',
            'estado'=>'required',
        ]);
        $oficina=oficina::findOrFail($request->oficina);
        try{
            $oficina->estado=$request->estado;
            $oficina->save();
            return true;
        }catch(Exception $e){
            return response()->json(['message'=>'Error al cambiar estado'],405);
        }
    }
}
