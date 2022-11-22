<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\oficina;
use App\Models\role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function current(Request $request)
    {
        $user=$request->user();
        //$rol=role::where('id',$user->rol_id)->first();
        $oficina=oficina::where('id',$user->oficina_id)->first();
        return response()->json([
            'id'=>$user->id,
            'email'=>$user->email,
            'name'=>$user->name,
            'rol_id'=>$user->rol_id,
            'oficina_id'=>$oficina?$oficina->id:null,
            'oficina'=>$oficina?$oficina->nombre:null,
        ]);
    }
}
