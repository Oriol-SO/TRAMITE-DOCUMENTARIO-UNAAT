<?php

namespace App\Http\Controllers;

use App\Models\oficina;
use Illuminate\Http\Request;

class OficinaController extends Controller
{
    public function fetch_oficinas(){
        return oficina::all();
    }
}
