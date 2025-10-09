<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CuocHopController extends Controller
{
    public function cuochop(){
        return view('truongkhoa.cuochopkhoa.cuochopkhoa');
    }
}
