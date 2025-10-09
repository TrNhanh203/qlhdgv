<div class="sidebar" id="sidebar">
  <div class="logo">
    <i class="bi bi-mortarboard-fill"></i>
    <span class="menu-text">EduSuperAdmin</span>
  </div>

  <ul class="nav flex-column">
  <!-- Quản lý trường -->
  <li class="nav-item">
    <a class="nav-link submenu-toggle" href="#quanlytruong">
      <i class="bi bi-building menu-icon"></i>
      <span class="menu-text">Quản lý trường</span>
      <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
    </a>
    <ul class="submenu" id="quanlytruong">
      <li><a class="nav-link" href="{{ route('superadmin.dashboard') }}">Tổng quan</a></li>
      <li><a class="nav-link" href="{{ route('superadmin.university.index') }}">Danh sách trường</a></li>
      <li><a class="nav-link" href="{{ route('superadmin.university_admins.index') }}">Quản lý Admin trường</a></li>
    </ul>
  </li>

  <!-- Quản lý hệ thống -->
  <li class="nav-item">
    <a class="nav-link submenu-toggle" href="#hethong">
      <i class="bi bi-gear-fill menu-icon"></i>
      <span class="menu-text">Hệ thống</span>
      <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
    </a>
    <ul class="submenu" id="hethong">
      
      <li><a class="nav-link" href="{{ route('superadmin.settings.roles') }}">Phân quyền</a></li>
      <li><a class="nav-link" href="{{ route('superadmin.settings.backup') }}">Sao lưu & Phục hồi</a></li>
    </ul>
</li>

  <!-- Báo cáo & thống kê -->
  <li class="nav-item">
    <a class="nav-link submenu-toggle" href="#baocao">
      <i class="bi bi-graph-up-arrow menu-icon"></i>
      <span class="menu-text">Báo cáo & Thống kê</span>
      <i class="bi bi-chevron-down ms-auto submenu-icon"></i>
    </a>
    <ul class="submenu" id="baocao">
      <li><a class="nav-link" href="{{ route('superadmin.reports.universities') }}">Báo cáo theo trường</a></li>
      <li><a class="nav-link" href="{{ route('superadmin.reports.audit') }}">Nhật ký hệ thống</a></li>
    </ul>
</li>


  <!-- Thông báo -->
<!--  <li class="nav-item">
    <a class="nav-link" href="{{ route('superadmin.notifications.index') }}">
      <i class="bi bi-bell-fill menu-icon"></i>
      <span class="menu-text">Thông báo hệ thống</span>
    </a>
  </li> -->
</ul>

</div>

<script>
  // Toggle Submenu
  document.querySelectorAll('.submenu-toggle').forEach(item => {
    item.addEventListener('click', function () {
      const parent = this.parentElement;
      parent.classList.toggle('open');
    });
  });

  // Toggle Sidebar (ẩn/hiện toàn bộ sidebar)
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleSidebar'); // cần có nút ở header
  if(toggleBtn){
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
    });
  }
</script>
