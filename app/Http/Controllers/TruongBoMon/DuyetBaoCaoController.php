<?php

namespace App\Http\Controllers\TruongBoMon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DuyetBaoCaoController extends Controller
{
    public function hopchuyenmon(){
        return view('truongbomon.duyetbaocao.hopchuyenmon');
    }
    public function klcongviec(){
        return view('truongbomon.duyetbaocao.klcongviec');
    }
    public function bcketthuchocphan(){
        return view('truongbomon.duyetbaocao.bcketthuchocphan');
    }
}
