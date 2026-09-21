 <!-- Main Sidebar -->
 <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
    <div class="main-navbar">
      <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
        <a class="navbar-brand w-100 mr-0" href="#" style="line-height: 25px;">
          <div class="d-table m-auto">
            <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 120px;" src="{{ url('assets') }}/images/bg-silila.jpg" alt="SILILA">
            {{-- <span class="d-none d-md-inline ml-1">SILILA</span> --}}
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
          <a class="nav-link {{ ( request()->segment(2) == 'index' || empty(request()->segment(2)) )?'active':'' }}" href="{{ url('dashboard') }}">
            <i class="material-icons">map</i>
            <span>Map Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'lp2b' ? 'active' : '' }}" href="{{ route('dashboard.lp2b') }}">
                <i class="material-icons">analytics</i>
                <span>Data LP2B</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'lsd' ? 'active' : '' }}" href="{{ route('dashboard.lsd') }}">
                <i class="material-icons">analytics</i>
                <span>Data LSD</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'desa' ? 'active' : '' }}" href="{{ route('dashboardDesa') }}">
                <i class="material-icons">analytics</i>
                <span>Data Desa</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'kecamatan' ? 'active' : '' }}" href="{{ route('dashboardKecamatan') }}">
                <i class="material-icons">analytics</i>
                <span>Data Kecamatan</span>
            </a>
        </li>
        @endif
        
        @if(auth()->user()->role == 1 || auth()->user()->role == 3)
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'history' ? 'active' : '' }}" href="{{ route('dashboard.history') }}">
                <i class="material-icons">history</i>
                <span>Riwayat Pencarian</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'chat' ? 'active' : '' }}" href="{{ route('dashboard.chat') }}">
                <i class="material-icons">chat</i>
                <span>Live Chat</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) == 'log' ? 'active' : '' }}" href="{{ route('dashboard.log') }}">
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
