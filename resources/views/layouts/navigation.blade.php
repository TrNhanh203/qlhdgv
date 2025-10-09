<nav class="navbar navbar-light custom-gradient-navbar shadow px-3" style="position: fixed; top: 0; left: 250px; width: calc(100% - 250px); height: 0px; z-index: 1000;">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        {{-- Left: Logo + Tên hệ thống --}}
        <div class="d-flex align-items-center gap-2">
            <a class="navbar-brand p-0 m-0" href="{{ route('admin.dashboard') }}">
                
            </a>
            <span class="fw-semibold text-dark"></span>
        </div>

        {{-- Right: Tài khoản --}}
        <ul class="navbar-nav d-flex flex-row align-items-center gap-3 mb-0">
            @guest
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Cài đặt tài khoản</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>