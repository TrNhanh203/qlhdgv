<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChuongTrinhHocPhanController extends Controller
{
    public function chuongtrinh(){
        return view('truongkhoa.chuongtrinhhocphan.chuongtrinh');
    }
    public function hocphan(){
        return view('truongkhoa.chuongtrinhhocphan.hocphan');
    }
}
