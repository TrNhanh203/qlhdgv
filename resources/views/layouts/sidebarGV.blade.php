<div class="sidebar" id="sidebar">
    <div class="logo">
        <i class="bi bi-mortarboard-fill"></i>
        <span class="menu-text">
            {{ auth()->user()?->university?->university_name ?? 'Chưa có trường' }}

        </span>


    </div>

    <ul class="nav flex-column">

        <!-- soạn đề cương học phần được phân công -->
        <li class="nav-item">
            <a class="nav-link submenu-toggle" href="#soanDeCuong">
                <i class="bi bi-file-earmark-text-fill menu-icon"></i>
                <span class="menu-text">Đề cương học phần</span>
                <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
            </a>
            <ul class="submenu" id="soanDeCuong">
                <li><a class="nav-link" href="#">Danh sách đề cương</a></li>
                <li><a class="nav-link" href="#">Đề cương được phân công</a></li>
            </ul>
        </li>

        <!-- Xem lịch giảng dạy & coi thi -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#lichGiangDay">
        <i class="bi bi-calendar3 menu-icon"></i>
        <span class="menu-text">Xem lịch giảng dạy & coi thi</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="lichGiangDay">
        <li><a class="nav-link" href="#">Lịch giảng dạy</a></li>
        <li><a class="nav-link" href="#">Lịch coi thi</a></li>
      </ul>
    </li> --}}

        <!-- Xem lịch phòng học & lịch thi -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#lichPhongThi">
        <i class="bi bi-building menu-icon"></i>
        <span class="menu-text">Xem lịch phòng học & lịch thi</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="lichPhongThi">
        <li><a class="nav-link" href="#">Lịch phòng học</a></li>
        <li><a class="nav-link" href="#">Lịch thi chi tiết</a></li>
      </ul>
    </li> --}}

        <!-- Khai báo khối lượng công việc khác -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#khoiLuongKhac">
        <i class="bi bi-list-task menu-icon"></i>
        <span class="menu-text">Khai báo khối lượng công việc khác</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="khoiLuongKhac">
        <li><a class="nav-link" href="#">Danh sách công việc</a></li>
        <li><a class="nav-link" href="#">Cập nhật trạng thái</a></li>
      </ul>
    </li> --}}

        <!-- Tham gia họp chuyên môn -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#hopChuyenMon">
        <i class="bi bi-people-fill menu-icon"></i>
        <span class="menu-text">Tham gia họp chuyên môn</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="hopChuyenMon">
        <li><a class="nav-link" href="#">Lịch họp</a></li>
        <li><a class="nav-link" href="#">Nội dung họp</a></li>
      </ul>
    </li> --}}

        <!-- Đăng ký demo giảng dạy -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#demoGiangDay">
        <i class="bi bi-play-btn-fill menu-icon"></i>
        <span class="menu-text">Đăng ký demo giảng dạy</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="demoGiangDay">
        <li><a class="nav-link" href="#">Đăng ký mới</a></li>
        <li><a class="nav-link" href="#">Theo dõi trạng thái</a></li>
      </ul>
    </li> --}}

        <!-- Cập nhật tiến độ giảng dạy -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#tienDoGiangDay">
        <i class="bi bi-graph-up menu-icon"></i>
        <span class="menu-text">Cập nhật tiến độ giảng dạy</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="tienDoGiangDay">
        <li><a class="nav-link" href="#">Tiến độ môn học</a></li>
        <li><a class="nav-link" href="#">Hoạt động chuyên môn</a></li>
      </ul>
    </li> --}}

        <!-- Xem thông tin môn học/học phần -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#monHocPhanCong">
        <i class="bi bi-journal-bookmark-fill menu-icon"></i>
        <span class="menu-text">Xem thông tin môn học/học phần</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="monHocPhanCong">
        <li><a class="nav-link" href="#">Danh sách môn học</a></li>
        <li><a class="nav-link" href="#">Chi tiết học phần</a></li>
      </ul>
    </li> --}}

        <!-- Nộp báo cáo kết thúc học phần -->
        {{-- <li class="nav-item">
      <a class="nav-link submenu-toggle" href="#baoCaoKetThuc">
        <i class="bi bi-file-earmark-text-fill menu-icon"></i>
        <span class="menu-text">Nộp báo cáo kết thúc học phần</span>
        <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
      </a>
      <ul class="submenu" id="baoCaoKetThuc">
        <li><a class="nav-link" href="#">Tạo báo cáo</a></li>
        <li><a class="nav-link" href="#">Theo dõi trạng thái</a></li>
      </ul>
    </li> --}}

    </ul>
</div>

<style>
    .submenu {
        display: none;
        padding-left: 1.5rem;
    }

    .submenu.show {
        display: block;
    }

    .menu-icon {
        margin-right: 0.5rem;
    }
</style>

<script>
    // Toggle Submenu
    document.querySelectorAll('.submenu-toggle').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault(); // ngăn scroll khi click href="#id"
            const submenu = this.nextElementSibling; // ul.submenu
            if (submenu) submenu.classList.toggle('show');
            this.querySelector('.submenu-icon').classList.toggle('bi-chevron-up');
            this.querySelector('.submenu-icon').classList.toggle('bi-chevron-down');
        });
    });

    // Toggle Sidebar (ẩn/hiện sidebar)
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
</script>

<script>
    document.querySelectorAll('.submenu-toggle').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            if (submenu) submenu.classList.toggle('show');
            const icon = this.querySelector('.submenu-icon');
            if (icon) icon.classList.toggle('rotate');
        });
    });
</script>
