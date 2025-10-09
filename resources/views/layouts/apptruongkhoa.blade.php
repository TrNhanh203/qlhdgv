<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Hệ thống Quản lý Đại học')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { font-family: 'Nunito', sans-serif; background-color: #f5f6fa; margin: 0; transition: background 0.3s, color 0.3s; }

    /* Dark Mode */
    body.dark-mode { background-color: #1e1e2f; color: #f1f1f1; }
    body.dark-mode .header { background: #2c2c3e; border-bottom-color: #444; }
    body.dark-mode .card { background: #2c2c3e; color: #f1f1f1; }
    body.dark-mode .dashboard-card { opacity: 0.9; }
    body.dark-mode .sidebar { background: linear-gradient(to bottom, #2b2b4f, #1a3a3a); }
    body.dark-mode .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); }
    body.dark-mode .nav-link { color: #eee; }
    body.dark-mode .submenu .nav-link { color: #ddd; }

    /* Sidebar */
    .sidebar { width: 300px; height: 100vh; background: linear-gradient(to bottom, #4e73df, #1cc88a);
      position: fixed; top: 0; left: 0; overflow-y: auto; box-shadow: 2px 0 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease; z-index: 1050; color: #fff; }
    .sidebar.collapsed { width: 45px; }
    .sidebar .logo { display: flex; align-items: center; justify-content: center; height: 70px;
      font-size: 20px; font-weight: bold; }
    .sidebar .nav-link { color: #fff; font-weight: 500; padding: 12px 20px; display: flex; align-items: center;
      transition: all 0.3s ease; cursor: pointer; border-radius: 6px; margin: 4px 10px; text-decoration: none; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); }
    .menu-icon { font-size: 1.2rem; margin-right: 12px; }
    .menu-text { transition: opacity 0.3s ease; }
    .sidebar.collapsed .menu-text { display: none; }
    .sidebar.collapsed .nav-link { justify-content: center; }

    /* Submenu */
    .submenu { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, opacity 0.3s ease;
      opacity: 0; list-style: none; padding-left: 20px; }
    .submenu.show { max-height: 500px; opacity: 1; }
    .submenu .nav-link { font-size: 0.9rem; }

    /* Main Content */
    .main-content { margin-left: 300px; transition: margin-left 0.3s ease; padding: 20px; }
    .main-content.collapsed { margin-left: 70px; }

    /* Header */
    .header { height: 60px; background: #fff; border-bottom: 1px solid #ddd; display: flex; align-items: center;
      justify-content: space-between; padding: 0 20px; position: sticky; top: 0; z-index: 1000; }
    .header .user { display: flex; align-items: center; gap: 10px; }
    .header img { width: 32px; height: 32px; border-radius: 50%; }
.dashboard-icon {
  font-size: 2.5rem;
  opacity: 0.8;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.dashboard-card:hover .dashboard-icon {
  transform: rotate(15deg) scale(1.2);
  opacity: 1;
}

.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}
    /* Dashboard cards */
    .dashboard-card { border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer; padding: 20px; color: #fff; min-height: 140px; }
    .dashboard-card:hover { transform: translateY(-5px); box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
    .card-primary { background: #4e73df; }
    .card-success { background: #1cc88a; }
    .card-warning { background: #f6c23e; color: #000; }
    .card-info { background: #36b9cc; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  @include('layouts.sidebartruongkhoa')

<div class="main-content" id="mainContent">
  <!-- Header -->
  <div class="header">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-sm btn-outline-primary" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <!-- Nút Dark Mode -->
      <button class="btn btn-sm btn-outline-secondary" id="toggleDarkMode">
        <i class="bi bi-moon-stars" id="darkModeIcon"></i>
      </button>
    </div>

    <!-- Hiển thị ngày giờ -->
    <div class="datetime me-3 fw-bold">
      <span id="dateTimeText"></span>
    </div>

    <div class="user">
      <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="me-2">{{ $user->name ?? 'Trưởng Khoa' }}</span>
        <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://i.pravatar.cc/100' }}"
     alt="Avatar"
     class="rounded-circle"
     style="width:32;height:32;object-fit:cover;"
     onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li>
          <a class="dropdown-item" href="{{ route('profile.show') }}">
            <i class="bi bi-person-fill me-2"></i> Hồ sơ
          </a>
         
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
              <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
            </button>
          </form>
        </li>
      </ul>

    </div>
    
  </div>

  <!-- Nội dung trang -->
  <div class="container-fluid mt-4">
    @yield('content')
  </div>
</div>

<script>
  function updateDateTime() {
    const now = new Date();

    // Thứ
    const days = ["Chủ nhật","Thứ 2","Thứ 3","Thứ 4","Thứ 5","Thứ 6","Thứ 7"];
    const dayName = days[now.getDay()];

    // Ngày tháng năm
    const dateStr = `${dayName}, ${now.getDate().toString().padStart(2, '0')}/${(now.getMonth()+1).toString().padStart(2, '0')}/${now.getFullYear()}`;

    // Giờ phút giây
    const timeStr = now.toLocaleTimeString('vi-VN');

    document.getElementById('dateTimeText').textContent = `${dateStr} - ${timeStr}`;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Toggle Sidebar
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
      document.getElementById('mainContent').classList.toggle('collapsed');
    });

    // Toggle Submenu
    document.querySelectorAll('.submenu-toggle').forEach(toggle => {
      toggle.addEventListener('click', function () {
        const submenu = this.nextElementSibling;
        submenu.classList.toggle('show');
      });
    });

    // Dark Mode
    const body = document.body;
    const darkBtn = document.getElementById('toggleDarkMode');
    const darkIcon = document.getElementById('darkModeIcon');

    // Load trạng thái từ localStorage
    if(localStorage.getItem('dark-mode') === 'enabled') {
      body.classList.add('dark-mode');
      darkIcon.classList.remove('bi-moon-stars');
      darkIcon.classList.add('bi-sun');
    }

    darkBtn.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      if(body.classList.contains('dark-mode')) {
        localStorage.setItem('dark-mode', 'enabled');
        darkIcon.classList.remove('bi-moon-stars');
        darkIcon.classList.add('bi-sun');
      } else {
        localStorage.setItem('dark-mode', 'disabled');
        darkIcon.classList.remove('bi-sun');
        darkIcon.classList.add('bi-moon-stars');
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
