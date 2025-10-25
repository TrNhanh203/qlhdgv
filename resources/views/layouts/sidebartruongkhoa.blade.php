<div class="sidebar" id="sidebar">
    <div class="logo">
        <i class="bi bi-mortarboard-fill"></i>
        <span class="menu-text">
            {{ auth()->user()?->university?->university_name ?? 'Chưa có trường' }}

        </span>


    </div>
    <!--<span >
    {{ auth()->user()->getFacultyName() ?? 'Chưa có khoa' }}
</span>-->
    <ul class="nav flex-column">
        <!-- Quản lý thông tin khoa -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('truongkhoa.dashboard') }}">
                <i class="bi bi-building menu-icon"></i>
                <span class="menu-text">Tổng Quan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-journal-bookmark menu-icon"></i>
                <span class="menu-text">Thông Tin Chi Tiết</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">

                <li><a class="nav-link" href="{{ route('truongkhoa.bomon.index') }}">Bộ môn</a></li>
                <li><a class="nav-link" href="{{ route('truongkhoa.giangvien.index') }}">Giảng viên</a></li>
                <!--<li><a class="nav-link" href="#">Ma trận đóng góp</a></li>-->
            </ul>
        </li>


        <!-- Chương trình & Học phần -->
        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-journal-bookmark menu-icon"></i>
                <span class="menu-text">Chương trình & Học phần</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="{{ route('truongkhoa.chuongtrinhdaotao.index') }}">Chương trình đào
                        tạo</a></li>
                <li><a class="nav-link" href="{{ route('truongkhoa.chuongtrinhhocphan.hocphan') }}">Học phần</a></li>

            </ul>
        </li>




        <!-- Lịch thi & Coi thi -->
        {{-- <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-calendar-check menu-icon"></i>
                <span class="menu-text">Lịch thi & Coi thi</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="{{ route('truongkhoa.lichthicoithi.lichthi') }}">Lịch thi</a></li>
                <li><a class="nav-link" href="{{ route('truongkhoa.lichthicoithi.coithi') }}">Giảng viên Coi thi</a>
                </li>

            </ul>
        </li> --}}
        <!-- Phân công & Kế hoạch giảng dạy -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('truongkhoa.phanconggiangday.phancong') }}">
                <i class="bi bi-person-check menu-icon"></i>
                <span class="menu-text">Phân công giảng dạy</span>
            </a>
        </li>

        <!-- Cuộc họp khoa -->
        {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('truongkhoa.cuochopkhoa.cuochopkhoa') }}">
            <i class="bi bi-people-fill menu-icon"></i>
            <span class="menu-text">Cuộc họp khoa</span>
        </a>
    </li> --}}

        <!-- Khối lượng công việc -->
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('truongkhoa.khoiluongcongviec.klcongviec') }}">
                <i class="bi bi-clipboard-data menu-icon"></i>
                <span class="menu-text">Khối lượng công việc</span>
            </a>
        </li> --}}

        <!-- Báo cáo & Thống kê -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('truongkhoa.baocaothongke.baocao') }}">
                <i class="bi bi-bar-chart-line menu-icon"></i>
                <span class="menu-text">Báo cáo & Thống kê</span>
            </a>
        </li>
    </ul>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
</script>
