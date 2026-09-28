 <!-- Main Sidebar -->
 <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
    <div class="main-navbar">
      <nav class="navbar align-items-stretch flex-md-nowrap p-0">
        <a href="#" class="desktop-toggle-sidebar-action nav-link nav-link-icon text-center border-right d-none d-md-block" style="padding: 0.85rem 1rem;">
          <i class="material-icons">&#xE5D2;</i>
        </a>
        <a class="navbar-brand mr-0 d-flex align-items-center justify-content-center" href="{{ url('dashboard') }}" style="line-height: 25px; flex-grow: 1; text-decoration: none;">
          <div class="d-flex align-items-center py-2 px-3">
            <div style="background: linear-gradient(135deg, #10b981, #059669); width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16,185,129,0.35); margin-right: 10px;">
              <i class="material-icons text-white" style="font-size: 20px;">terrain</i>
            </div>
            <div class="d-flex flex-column text-left">
              <span style="font-family: var(--font-heading); font-weight: 800; font-size: 18px; color: #ffffff; letter-spacing: 0.5px;">SILILA</span>
              <span style="font-size: 10px; color: #fef08a; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">Kab. Banyuwangi</span>
            </div>
          </div>
        </a>
        <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none p-3 text-white" style="cursor: pointer;">
          <i class="material-icons">&#xE5C4;</i>
        </a>
      </nav>
    </div>

    <div class="nav-wrapper">
      <ul class="nav flex-column">
        @if(auth()->user()->role == 1)
        <li class="nav-item">
          <small class="text-uppercase px-3 py-2 d-block" style="color: #a7f3d0; font-size: 10.5px; font-weight: 800; letter-spacing: 0.1em;">Data Geospasial</small>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
            <i class="material-icons">travel_explore</i>
            <span>Map Overview</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.lp2b') ? 'active' : '' }}" href="{{ route('dashboard.lp2b') }}">
                <i class="material-icons">grass</i>
                <span>Data LP2B</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.lsd') ? 'active' : '' }}" href="{{ route('dashboard.lsd') }}">
                <i class="material-icons">verified_user</i>
                <span>Data LSD</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboardDesa') ? 'active' : '' }}" href="{{ route('dashboardDesa') }}">
                <i class="material-icons">holiday_village</i>
                <span>Data Desa</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboardKecamatan') ? 'active' : '' }}" href="{{ route('dashboardKecamatan') }}">
                <i class="material-icons">domain</i>
                <span>Data Kecamatan</span>
            </a>
        </li>
        @endif
        
        @if(auth()->user()->role == 1 || auth()->user()->role == 3)
        <li class="nav-item mt-3">
          <small class="text-uppercase px-3 py-2 d-block" style="color: #a7f3d0; font-size: 10.5px; font-weight: 800; letter-spacing: 0.1em;">Manajemen & Layanan</small>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.history') ? 'active' : '' }}" href="{{ route('dashboard.history') }}">
                <i class="material-icons">history</i>
                <span>Riwayat Pencarian</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard.chat') ? 'active' : '' }}" href="{{ route('dashboard.chat') }}">
                <i class="material-icons">chat_bubble</i>
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
      </ul>
    </div>
  </aside>
