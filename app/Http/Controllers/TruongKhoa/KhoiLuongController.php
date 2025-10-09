<?php

namespace App\Http\Controllers\TruongKhoa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KhoiLuongController extends Controller
{
    public function khoiluong(){
        return view('truongkhoa.khoiluongcongviec.klcongviec');
    }
}
