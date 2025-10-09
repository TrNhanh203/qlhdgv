<div class="sidebar" id="sidebar">
    <div class="logo">
        <i class="bi bi-mortarboard-fill"></i>
        <span class="menu-text">
            {{ auth()->user()?->university?->university_name ?? 'Chưa có trường' }}

        </span>

    </div>

    <ul class="nav flex-column">
        <!-- 1. Trường / Khoa / Bộ môn -->
        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-building menu-icon"></i>
                <span class="menu-text">Trường / Khoa / Bộ môn</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="{{ route('admin.dashboard') }}">Thông tin trường</a></li>
                <li><a class="nav-link" href="{{ route('admin.khoa.index') }}">Khoa / Viện</a></li>
                <li><a class="nav-link" href="{{ route('admin.bomon.index') }}">Bộ môn</a></li>
                <li><a class="nav-link" href="{{ route('admin.giangvien.index') }}">Giảng viên</a></li>
                <li><a class="nav-link" href="{{ route('admin.namhochocky.index') }}">Học kỳ / Năm học</a></li>
                <!--<li><a class="nav-link" href="#">Trạng thái (Status)</a></li>-->
            </ul>
        </li>



        <!-- 3. Chương trình đào tạo -->
        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-journal-bookmark menu-icon"></i>
                <span class="menu-text">Chương trình đào tạo</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="{{ route('admin.chuongtrinhdaotao.index') }}">Chương trình đào tạo</a>
                </li>
                <li><a class="nav-link" href="{{ route('admin.hedaotao.index') }}">Hệ đào tạo</a></li>
                <!--<li><a class="nav-link" href="#">Ma trận đóng góp</a></li>-->
            </ul>
        </li>

        <!-- 4. Phòng học / Phòng thi -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle">
        <i class="bi bi-door-open menu-icon"></i>
        <span class="menu-text">Phòng học & Phòng thi</span>
        <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a class="nav-link" href="{{ route('admin.phonghoc.index') }}">Phòng học</a></li>
        <li><a class="nav-link" href="{{ route('admin.phongthi.index') }}">Phòng thi</a></li>
      </ul>
    </li> --}}

        <!-- 5. Học phần / Nhóm học phần -->
        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-collection menu-icon"></i>
                <span class="menu-text">Học phần / Nhóm học phần</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="{{ route('admin.hocphan.index') }}">Học phần</a></li>
                <li><a class="nav-link" href="{{ route('admin.nhomhocphan.index') }}">Nhóm học phần</a></li>
                <li><a class="nav-link" href="{{ route('admin.decuonghocphan.index') }}">Đề cương học phần</a></li>

            </ul>
        </li>


        <li class="nav-item">
            <a class="nav-link submenu-toggle">
                <i class="bi bi-pencil-square menu-icon"></i>
                <span class="menu-text">Thông tin giảng dạy</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="submenu">
                <li><a class="nav-link" href="">Danh sách giảng dạy</a></li>
                <li><a class="nav-link" href="">Phân công giảng dạy</a></li>
            </ul>
        </li>
        <!-- 6. Kỳ thi & Hình thức -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle">
        <i class="bi bi-pencil-square menu-icon"></i>
        <span class="menu-text">Thông tin kỳ thi</span>
        <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
      </a>
      <ul class="submenu">
        <li><a class="nav-link" href="{{ route('admin.thongtindotthi.index') }}">Kỳ thi, Hình thức, Loại kỳ thi</a></li>
        <li><a class="nav-link" href="{{ route('admin.lichthi.index') }}">Lịch gác thi</a></li>
        <li><a class="nav-link" href="#">Sắp xếp giảng viên coi thi</a></li>
      </ul>
    </li> --}}


        <!-- 8. Tài khoản -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.taikhoan.index') }}">
                <i class="bi bi-person-badge menu-icon"></i>
                <span class="menu-text">Quản lý tài khoản</span>
            </a>
        </li>

        <!-- 9. Tệp đính kèm -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-paperclip menu-icon"></i>
                <span class="menu-text">Tệp đính kèm</span>
            </a>
        </li>

        <!-- 10. Báo cáo -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="bi bi-graph-up menu-icon"></i>
                <span class="menu-text">Báo cáo</span>
            </a>
        </li>
    </ul>

</div>

<script>
    // Toggle Submenu
    document.querySelectorAll('.submenu-toggle').forEach(item => {
        item.addEventListener('click', function() {
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });

    // Toggle Sidebar (ẩn/hiện toàn bộ sidebar)
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar'); // cần có nút ở header
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
</script>
