<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\FirstLoginController;
use Illuminate\Support\Facades\Auth;

// ADMIN Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TruongController;
use App\Http\Controllers\Admin\KhoaController;
use App\Http\Controllers\Admin\BoMonController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\NhomHocPhanController;
use App\Http\Controllers\Admin\GiangVienController;
use App\Http\Controllers\Admin\NamHocHocKyController;
use App\Http\Controllers\Admin\PhongHocController;
use App\Http\Controllers\Admin\KyThiController;
use App\Http\Controllers\Admin\HinhThucThiController;
use App\Http\Controllers\Admin\LoaiKyThiController;
use App\Http\Controllers\Admin\HocPhanController;
use App\Http\Controllers\Admin\HeDaoTaoController;
use App\Http\Controllers\Admin\ThongTinDotThiController;
use App\Http\Controllers\Admin\PhongThiController;
use App\Http\Controllers\Admin\TaiKhoanController;
use App\Http\Controllers\Admin\LichThiController;
use App\Http\Controllers\Admin\CourseSyllabusController;


// SUPERADMIN Controllers
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UniversityController;
use App\Http\Controllers\SuperAdmin\UniversityAdminController;
use App\Http\Controllers\SuperAdmin\SystemController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\SuperAdmin\SystemNotificationController;


// OTHER ROLES
use App\Http\Controllers\TruongKhoa\DashboardController as TruongKhoaDashboardController;
use App\Http\Controllers\TruongKhoa\BoMonController as TruongKhoaBoMonController;
use App\Http\Controllers\TruongKhoa\GiangVienController as TruongKhoaGiangVienController;
use App\Http\Controllers\TruongKhoa\ChuongTrinhHocPhanController;
use App\Http\Controllers\TruongKhoa\LichThiCoiThiController;
use App\Http\Controllers\TruongKhoa\KhoiLuongController;
use App\Http\Controllers\TruongKhoa\PhanCongController;
use App\Http\Controllers\TruongKhoa\CuocHopController;
use App\Http\Controllers\TruongKhoa\BaoCaoController;
use App\Http\Controllers\TruongKhoa\EducationProgramController;


use App\Http\Controllers\TruongBoMon\DashboardController as TruongBoMonDashboardController;
use App\Http\Controllers\TruongBoMon\QLGiangVienController;
use App\Http\Controllers\TruongBoMon\QLHocPhanController;
use App\Http\Controllers\TruongBoMon\DeXuatThiController;
use App\Http\Controllers\TruongBoMon\DuyetBaoCaoController;
use App\Http\Controllers\GiangVien\DashboardController as GiangVienDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/first-login/change-password', [FirstLoginController::class, 'showChangeForm'])
        ->name('first-login.form');
    Route::post('/first-login/change-password', [FirstLoginController::class, 'updatePassword'])
        ->name('first-login.update');
});

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        switch ($user->user_type) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'truongkhoa':
                return redirect()->route('truongkhoa.dashboard');
            case 'truongbomon':
                return redirect()->route('truongbomon.dashboard');
            case 'giangvien':
                return redirect()->route('giangvien.dashboard');
            default:
                return redirect()->route('profile.show');
        }
    }
    return view('welcome');
})->name('welcome');

// ====================== AUTH ======================
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
// ====================== DASHBOARD THEO ROLE ======================

// ====================== ADMIN ======================
Route::prefix('admin')
    ->middleware(['auth', 'role:admin', 'check.first.login'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // Cập nhật thông tin trường từ Dashboard (logo + mô tả)
        Route::put('/truong/{id}', [AdminDashboardController::class, 'updateUniversity'])->name('truong.update');

        // ======================= TRƯỜNG =======================
        Route::get('/truong', [TruongController::class, 'index'])->name('truong.index');
        Route::get('/truong/create', [TruongController::class, 'create'])->name('truong.create');
        Route::post('/truong', [TruongController::class, 'store'])->name('truong.store');
        Route::get('/truong/{id}/edit', [TruongController::class, 'edit'])->name('truong.edit');
        Route::delete('/truong/{id}', [TruongController::class, 'destroy'])->name('truong.destroy');

        // ======================= KHOA =======================
        Route::get('/khoa', [KhoaController::class, 'index'])->name('khoa.index');
        Route::post('/khoa/store', [KhoaController::class, 'store'])->name('khoa.store');
        Route::delete('/khoa/{id}', [KhoaController::class, 'destroy'])->name('khoa.destroy');
        Route::delete('/khoa', [KhoaController::class, 'destroyMultiple'])->name('khoa.destroyMultiple');
        Route::post('/truongkhoa/store', [KhoaController::class, 'storeTruongKhoa'])->name('truongkhoa.store');
        Route::post('/truongkhoa/delete-multiple', [KhoaController::class, 'destroyMultipleTruongKhoa'])->name('truongkhoa.deleteMultiple');
        // ======================= BỘ MÔN =======================
        Route::get('/bomon', [BoMonController::class, 'index'])->name('bomon.index');
        Route::post('/bomon/store', [BoMonController::class, 'store'])->name('bomon.store');
        Route::delete('/bomon/{id}', [BoMonController::class, 'destroy'])->name('bomon.destroy');
        Route::post('/bomon/destroy-multiple', [BoMonController::class, 'destroyMultiple'])->name('bomon.destroyMultiple');

        Route::post('/truongbomon/store', [BoMonController::class, 'storeTruongBoMon'])->name('truongbomon.store');
        Route::delete('/truongbomon/{id}', [BoMonController::class, 'destroyTruongBoMon'])->name('truongbomon.destroy');
        Route::post('/truongbomon/destroy-multiple', [BoMonController::class, 'destroyMultipleTruongBoMon'])
            ->name('truongbomon.destroyMultiple');
        Route::get('/bomon/getTruongBoMon/{id}', [BoMonController::class, 'getTruongBoMon'])
            ->name('bomon.getTruongBoMon');
        // ======================= CHƯƠNG TRÌNH ĐÀO TẠO =======================
        Route::get('/chuongtrinhdaotao', [CurriculumController::class, 'index'])->name('chuongtrinhdaotao.index');
        Route::get('/chuongtrinhdaotao/create', [CurriculumController::class, 'create'])->name('chuongtrinhdaotao.create');
        Route::post('/chuongtrinhdaotao', [CurriculumController::class, 'store'])->name('chuongtrinhdaotao.store');
        Route::get('/chuongtrinhdaotao/{id}/edit', [CurriculumController::class, 'edit'])->name('chuongtrinhdaotao.edit');
        Route::put('/chuongtrinhdaotao/{id}', [CurriculumController::class, 'update'])->name('chuongtrinhdaotao.update');
        Route::delete('/chuongtrinhdaotao/{id}', [CurriculumController::class, 'destroy'])->name('chuongtrinhdaotao.destroy');
        Route::post('/chuongtrinhdaotao/destroy-multiple', [CurriculumController::class, 'destroyMultiple'])->name('chuongtrinhdaotao.destroyMultiple');

        // ======================= HỆ ĐÀO TẠO =======================
        Route::get('/hedaotao', [HeDaoTaoController::class, 'index'])->name('hedaotao.index');
        Route::post('/hedaotao', [HeDaoTaoController::class, 'store'])->name('hedaotao.store');
        Route::put('/hedaotao/{code}', [HeDaoTaoController::class, 'update'])->name('hedaotao.update');
        Route::delete('/hedaotao/{code}', [HeDaoTaoController::class, 'destroy'])->name('hedaotao.destroy');

        // ======================= NHÓM HỌC PHẦN =======================
        Route::get('/nhomhocphan', [NhomHocPhanController::class, 'index'])->name('nhomhocphan.index');
        Route::post('/nhomhocphan', [NhomHocPhanController::class, 'store'])->name('nhomhocphan.store');
        Route::put('/nhomhocphan/{code}', [NhomHocPhanController::class, 'update'])->name('nhomhocphan.update');
        Route::delete('/nhomhocphan/{code}', [NhomHocPhanController::class, 'destroy'])->name('nhomhocphan.destroy');

        // ======================= HỌC PHẦN =======================
        Route::get('/hocphan', [HocPhanController::class, 'index'])->name('hocphan.index');
        Route::get('/hocphan/create', [HocPhanController::class, 'create'])->name('hocphan.create');
        Route::post('/hocphan', [HocPhanController::class, 'store'])->name('hocphan.store');
        Route::get('/hocphan/{id}/edit', [HocPhanController::class, 'edit'])->name('hocphan.edit');
        Route::put('/hocphan/{id}', [HocPhanController::class, 'update'])->name('hocphan.update');
        Route::delete('/hocphan/{id}', [HocPhanController::class, 'destroy'])->name('hocphan.destroy');
        ///////////////////////////////////////////////////
        Route::get('/decuonghocphan', [CourseSyllabusController::class, 'index'])->name('decuonghocphan.index');
        Route::post('/decuonghocphan/store', [CourseSyllabusController::class, 'store'])->name('decuonghocphan.store');
        Route::delete('/decuonghocphan/{id}/delete', [CourseSyllabusController::class, 'destroy'])->name('decuonghocphan.delete');
        Route::delete('/decuonghocphan/delete-multiple', [CourseSyllabusController::class, 'destroyMultiple'])->name('decuonghocphan.deleteMultiple');
        // ======================= PHÒNG HỌC =======================
        Route::get('/phonghoc', [PhongHocController::class, 'index'])->name('phonghoc.index');
        Route::post('/phonghoc', [PhongHocController::class, 'store'])->name('phonghoc.store');
        Route::delete('/phonghoc/{id}', [PhongHocController::class, 'destroy'])->name('phonghoc.destroy');
        Route::delete('/phonghoc', [PhongHocController::class, 'destroyMultiple'])->name('phonghoc.destroyMultiple');
        // ======================= PHÒNG THI =======================
        Route::get('/phongthi', [PhongThiController::class, 'index'])->name('phongthi.index');
        Route::post('/phongthi', [PhongThiController::class, 'store'])->name('phongthi.store');
        Route::delete('/phongthi/{id}', [PhongThiController::class, 'destroy'])->name('phongthi.destroy');
        Route::delete('/phongthi', [PhongThiController::class, 'destroyMultiple'])->name('phongthi.destroyMultiple');
        // ======================= LỊCH THI =======================
        Route::get('/lichthi', [LichThiController::class, 'index'])->name('lichthi.index');
        // ======================= NĂM HỌC & HỌC KỲ =======================
        Route::get('/namhochocky', [NamHocHocKyController::class, 'index'])->name('namhochocky.index');
        Route::post('/year/store', [NamHocHocKyController::class, 'storeYear'])->name('years.store');
        Route::delete('/year/{id}', [NamHocHocKyController::class, 'destroyYear'])->name('years.destroy');


        Route::post('/semester/store', [NamHocHocKyController::class, 'storeSemester'])->name('semesters.store');
        Route::delete('/semester/{id}', [NamHocHocKyController::class, 'destroySemester'])->name('semesters.destroy');
        Route::delete('/years/delete-multiple', [NamHocHocKyController::class, 'deleteYears'])
            ->name('years.deleteMultiple');

        // Xóa nhiều học kỳ
        Route::delete('/semesters/delete-multiple', [NamHocHocKyController::class, 'deleteSemesters'])
            ->name('semesters.deleteMultiple');
        // ======================= KỲ THI =======================
        Route::get('/thongtindotthi', [ThongTinDotThiController::class, 'index'])->name('thongtindotthi.index');

        // ======================= GIẢNG VIÊN =======================
        Route::get('/giangvien', [GiangVienController::class, 'index'])->name('giangvien.index');
        Route::post('/giangvien/store', [GiangVienController::class, 'store'])->name('giangvien.store');
        Route::put('/giangvien/{id}', [GiangVienController::class, 'update'])->name('giangvien.update');

        Route::delete('/giangvien/{id}', [GiangVienController::class, 'destroy'])->name('giangvien.destroy');
        Route::post('/giangvien/delete-multiple', [GiangVienController::class, 'destroyMultiple'])->name('giangvien.destroyMultiple');

        Route::post('/giangvien/import', [GiangVienController::class, 'import'])->name('giangvien.import');
        Route::get('/giangvien/export', [GiangVienController::class, 'export'])->name('giangvien.export');
        Route::get('/giangvien/template', [GiangVienController::class, 'template'])->name('giangvien.template');

        // ======================= TÀI KHOẢN =======================

        Route::get('/taikhoan', [TaiKhoanController::class, 'index'])->name('taikhoan.index');
        Route::post('/taikhoan', [TaiKhoanController::class, 'store'])->name('taikhoan.store');
        // ========================= PROFILE =========================
        Route::get('/profile', function () {
            return view('profile.edit');
        })->name('profile.edit');
        Route::get('/profile/update', function () {
            return view('profile.update');
        })->name('profile.update');
        Route::get('/profile/destroy', function () {
            return view('profile.destroy');
        })->name('profile.destroy');
    });

// ====================== SUPERADMIN ======================
Route::prefix('superadmin')
    ->middleware(['auth', 'role:superadmin', 'check.first.login'])
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])->name('dashboard');

        // Quản lý trường
        Route::get('/university', [UniversityController::class, 'index'])->name('university.index');
        Route::post('/university/store', [UniversityController::class, 'store'])->name('university.store');
        Route::delete('/university/{id}', [UniversityController::class, 'destroy'])->name('university.destroy');
        Route::delete('/university', [UniversityController::class, 'destroyMultiple'])->name('university.destroyMultiple');

        // Quản lý Admin trường
        Route::get('/university_admins', [UniversityAdminController::class, 'index'])->name('university_admins.index');
        Route::post('/university_admins/store', [UniversityAdminController::class, 'store'])->name('university_admins.store');
        Route::delete('/university_admins/{id}', [UniversityAdminController::class, 'destroy'])->name('university_admins.destroy');
        Route::delete('/university_admins', [UniversityAdminController::class, 'destroyMultiple'])->name('university_admins.destroyMultiple');

        Route::get('settings/roles', [SystemController::class, 'roles'])->name('settings.roles');
        Route::post('settings/roles/store', [SystemController::class, 'storeRole'])->name('settings.roles.store');
        Route::delete('settings/roles/destroy-multiple', [SystemController::class, 'destroyMultipleRoles'])->name('settings.roles.destroyMultiple');
        Route::get('settings/backup/download', [SystemController::class, 'downloadBackup'])->name('backup.download');
        Route::get('settings/backup', [SystemController::class, 'backup'])->name('settings.backup');
        Route::post('settings/backup/restore', [SystemController::class, 'restoreBackup'])->name('backup.restore');
        // Quản lý người dùng

        Route::get('reports/universities', [ReportController::class, 'byUniversity'])->name('reports.universities');
        Route::get('reports/audit-logs', [ReportController::class, 'auditLogs'])->name('reports.audit');
        Route::get('notifications/index', [SystemNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/store', [SystemNotificationController::class, 'store'])->name('notifications.store');
        Route::post('notifications/update', [SystemNotificationController::class, 'update'])->name('notifications.update');
        Route::post('notifications/delete', [SystemNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('notifications/delete-multiple', [SystemNotificationController::class, 'destroyMultiple'])->name('notifications.destroyMultiple');
    });

// ====================== TRƯỞNG KHOA ======================
Route::prefix('truongkhoa')
    ->middleware(['auth', 'role:truongkhoa', 'check.first.login'])
    ->name('truongkhoa.')
    ->group(function () {
        Route::get('/dashboard', [TruongKhoaDashboardController::class, 'dashboard'])->name('dashboard');

        // === Chương trình đào tạo ===
        Route::get('/chuongtrinhdaotao', [EducationProgramController::class, 'index'])->name('chuongtrinhdaotao.index');
        Route::post('/chuongtrinhdaotao/store', [EducationProgramController::class, 'store'])->name('chuongtrinhdaotao.store');
        Route::post('/chuongtrinhdaotao/delete-multiple', [EducationProgramController::class, 'destroyMultiple'])->name('chuongtrinhdaotao.destroyMultiple');

        Route::get('/bomon', [TruongKhoaBoMonController::class, 'index'])->name('bomon.index');
        Route::get('/giangvien', [TruongKhoaGiangVienController::class, 'index'])->name('giangvien.index');
        // Route::get('/chuongtrinhhocphan/chuongtrinh', [ChuongTrinhHocPhanController::class, 'chuongtrinh'])->name('chuongtrinhhocphan.chuongtrinh');
        Route::get('/chuongtrinhhocphan/hocphan', [ChuongTrinhHocPhanController::class, 'hocphan'])->name('chuongtrinhhocphan.hocphan');
        Route::get('/lichthicoithi/lichthi', [LichThiCoiThiController::class, 'lichthi'])->name('lichthicoithi.lichthi');
        Route::get('/lichthicoithi/coithi', [LichThiCoiThiController::class, 'coithi'])->name('lichthicoithi.coithi');
        Route::get('/khoiluongcongviec/klcongviec', [KhoiLuongController::class, 'khoiluong'])->name('khoiluongcongviec.klcongviec');
        Route::get('/phanconggiangday/phancong', [PhanCongController::class, 'phancong'])->name('phanconggiangday.phancong');
        Route::get('/cuochopkhoa/cuochop', [CuocHopController::class, 'cuochop'])->name('cuochopkhoa.cuochopkhoa');
        Route::get('/baocaothongke/baocao', [BaoCaoController::class, 'baocao'])->name('baocaothongke.baocao');
    });

// ====================== TRƯỞNG BỘ MÔN ======================
Route::prefix('truongbomon')
    ->middleware(['auth', 'role:truongbomon'])
    ->name('truongbomon.')
    ->group(function () {
        Route::get('/dashboard', [TruongBoMonDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/quanlygiangvien/dsgiangvien', [QLGiangVienController::class, 'dsgiangvien'])->name('quanlygiangvien.dsgiangvien');
        Route::get('/quanlygiangvien/phanconggiangday', [QLGiangVienController::class, 'phanconggiangday'])->name('quanlygiangvien.phanconggiangday');
        Route::get('/quanlygiangvien/theodoitiendo', [QLGiangVienController::class, 'theodoitiendo'])->name('quanlygiangvien.theodoitiendo');

        Route::get('/quanlyhocphan/dshocphan', [QLHocPhanController::class, 'dshocphan'])->name('quanlyhocphan.dshocphan');
        Route::post('/quanlyhocphan/dshocphan', [QLHocPhanController::class, 'store'])->name('quanlyhocphan.store');

        Route::get('/dexuathi/dexuatlichthi', [DeXuatThiController::class, 'dexuatlichthi'])->name('dexuathi.dexuatlichthi');
        Route::get('/dexuathi/dexuatdethi', [DeXuatThiController::class, 'dexuatdethi'])->name('dexuathi.dexuatdethi');
        Route::get('/duyetbaocao/hopchuyenmon', [DuyetBaoCaoController::class, 'hopchuyenmon'])->name('duyetbaocao.hopchuyenmon');
        Route::get('/duyetbaocao/klcongviec', [DuyetBaoCaoController::class, 'klcongviec'])->name('duyetbaocao.klcongviec');
        Route::get('/duyetbaocao/bcketthuchocphan', [DuyetBaoCaoController::class, 'bcketthuchocphan'])->name('duyetbaocao.bcketthuchocphan');
    });

// ====================== GIẢNG VIÊN ======================
Route::prefix('giangvien')
    ->middleware(['auth', 'role:giangvien'])
    ->name('giangvien.')
    ->group(function () {
        Route::get('/dashboard', [GiangVienDashboardController::class, 'dashboard'])->name('dashboard');
    });
