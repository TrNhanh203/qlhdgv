<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QLHocPhanController extends Controller
{
    public function dshocphan(){
        return view('truongbomon.quanlyhocphan.dshocphan');
    }
}
