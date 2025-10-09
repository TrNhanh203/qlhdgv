<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaoCaoController extends Controller
{
    public function baocao(){
        return view('truongkhoa.baocaothongke.baocao');
    }
}
