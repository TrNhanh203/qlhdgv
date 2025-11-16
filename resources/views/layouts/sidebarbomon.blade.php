<div class="sidebar" id="sidebar">
    <div class="logo">
        <i class="bi bi-mortarboard-fill"></i>
        <span class="menu-text">
            {{ auth()->user()?->university?->university_name ?? 'Chưa có trường' }}

        </span>


    </div>

    <ul class="nav flex-column">

        <!-- Thông tin & quản lý bộ môn -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('truongbomon.dashboard') }}" onclick="toggleSubmenu('infoBoMon')">
                <i class="bi bi-building menu-icon"></i>
                <span class="menu-text">Tổng quan</span>
            </a>

        </li>

        <!-- Quản lý giảng viên & phân công -->
        <li class="nav-item">
            <a class="nav-link" href="#phanCongGV" onclick="toggleSubmenu('phanCongGV')">
                <i class="bi bi-person-badge menu-icon"></i>
                <span class="menu-text">Quản lý giảng viên</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="phanCongGV">
                <li><a class="nav-link" href="{{ route('truongbomon.quanlygiangvien.dsgiangvien') }}">Danh sách giảng
                        viên</a></li>
                <li><a class="nav-link" href="{{ route('truongbomon.quanlygiangvien.phanconggiangday') }}">Phân công
                        giảng dạy</a></li>
                <li><a class="nav-link" href="{{ route('truongbomon.quanlygiangvien.theodoitiendo') }}">Theo dõi tiến
                        độ</a></li>
            </ul>
        </li>

        <!-- Phân công soạn đề cương -->
        <li class="nav-item">
            <a class="nav-link" href="#phanCongDeCuong" onclick="toggleSubmenu('phanCongDeCuong')">
                <i class="bi bi-journal-bookmark menu-icon"></i>
                <span class="menu-text">Quản lý đề cương</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="phanCongDeCuong">
                <li><a class="nav-link" href="{{ route('truongbomon.quanlyhocphan.phancongdecuong.index') }}">Đề cương
                        theo học phần</a></li>




            </ul>

        </li>

        <!-- Quản lý môn học -->
        <li class="nav-item">
            <a class="nav-link" href="#monHocBoMon" onclick="toggleSubmenu('monHocBoMon')">
                <i class="bi bi-journal-bookmark menu-icon"></i>
                <span class="menu-text">học phần thuộc bộ môn</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="monHocBoMon">
                <li><a class="nav-link" href="{{ route('truongbomon.quanlyhocphan.dshocphan') }}">Danh sách học phần</a>
                </li>
            </ul>
        </li>

        <!-- Quản lý & đề xuất thi -->
        <li class="nav-item">
            <a class="nav-link" href="#thiBoMon" onclick="toggleSubmenu('thiBoMon')">
                <i class="bi bi-calendar-check menu-icon"></i>
                <span class="menu-text">Đề xuất thi</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="thiBoMon">
                <li><a class="nav-link" href="{{ route('truongbomon.dexuathi.dexuatlichthi') }}">Đề xuất lịch thi</a>
                </li>
                <li><a class="nav-link" href="{{ route('truongbomon.dexuathi.dexuatdethi') }}">Đề xuất đề thi</a></li>
            </ul>
        </li>

        <!-- Duyệt công việc, báo cáo -->
        <li class="nav-item">
            <a class="nav-link" href="#duyetBaoCao" onclick="toggleSubmenu('duyetBaoCao')">
                <i class="bi bi-file-earmark-check menu-icon"></i>
                <span class="menu-text">Duyệt & Báo cáo</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="duyetBaoCao">
                <li><a class="nav-link" href="{{ route('truongbomon.duyetbaocao.hopchuyenmon') }}">Duyệt họp chuyên
                        môn</a></li>
                <li><a class="nav-link" href="{{ route('truongbomon.duyetbaocao.klcongviec') }}">Duyệt khối lượng công
                        việc</a></li>
                <li><a class="nav-link" href="{{ route('truongbomon.duyetbaocao.bcketthuchocphan') }}">Duyệt báo cáo
                        kết thúc học phần</a></li>
            </ul>
        </li>

    </ul>
</div>

<style>
    .menu-icon {
        font-size: 1.3rem;
        margin-right: 10px;
        transition: transform 0.3s ease;
    }

    .nav-link:hover .menu-icon {
        transform: rotate(20deg) scale(1.2);
    }

    .submenu-icon {
        transition: transform 0.3s ease;
    }

    .nav-item.open>.nav-link .submenu-icon {
        transform: rotate(180deg);
    }
</style>

<script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        const parentItem = submenu.parentElement;

        submenu.classList.toggle('show');
        parentItem.classList.toggle('open');
    }
</script>
