 <!-- Main Sidebar -->
 <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
    <div class="main-navbar">
      <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
        <a href="#" class="desktop-toggle-sidebar-action nav-link nav-link-icon text-center border-right d-none d-md-block" style="padding: 0.85rem 1rem; color: #074173;">
          <i class="material-icons">&#xE5D2;</i>
        </a>
        <a class="navbar-brand mr-0" href="#" style="line-height: 25px; flex-grow: 1;">
          <div class="d-table m-auto">
            <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 120px;" src="{{ url('assets') }}/images/bg-silila.jpg" alt="SILILA">
          </div>
        </a>
        <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
          <i class="material-icons">&#xE5C4;</i>
        </a>
      </nav>
    </div>
    <form action="#" class="main-sidebar__search w-100 border-right d-sm-flex d-md-none d-lg-none">
      <div class="input-group input-group-seamless ml-3">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fas fa-search"></i>
          </div>
        </div>
        <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search"> </div>
    </form>
    <div class="nav-wrapper">
      <ul class="nav flex-column">
        @if(auth()->user()->role == 1)
        <li class="nav-item">
          <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
            <i class="material-icons">map</i>
            <span>Map Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.lp2b') ? 'active' : '' }}" href="{{ route('dashboard.lp2b') }}">
                <i class="material-icons">analytics</i>
                <span>Data LP2B</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.lsd') ? 'active' : '' }}" href="{{ route('dashboard.lsd') }}">
                <i class="material-icons">analytics</i>
                <span>Data LSD</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboardDesa') ? 'active' : '' }}" href="{{ route('dashboardDesa') }}">
                <i class="material-icons">analytics</i>
                <span>Data Desa</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboardKecamatan') ? 'active' : '' }}" href="{{ route('dashboardKecamatan') }}">
                <i class="material-icons">analytics</i>
                <span>Data Kecamatan</span>
            </a>
        </li>
        @endif
        
        @if(auth()->user()->role == 1 || auth()->user()->role == 3)
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.history') ? 'active' : '' }}" href="{{ route('dashboard.history') }}">
                <i class="material-icons">history</i>
                <span>Riwayat Pencarian</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.chat') ? 'active' : '' }}" href="{{ route('dashboard.chat') }}">
                <i class="material-icons">chat</i>
                <span>Live Chat</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.log') ? 'active' : '' }}" href="{{ route('dashboard.log') }}">
                <i class="material-icons">schedule</i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        @endif
        {{-- <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'profile' ? 'active' : '' }}" href="{{ route('dashboard.profile') }}">
                <i class="material-icons">account_circle</i>
                <span>Profile</span>
            </a>
        </li> --}}

      </ul>
    </div>
  </aside>
