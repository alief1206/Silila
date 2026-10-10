 <!-- Main Sidebar -->
 <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
    <div class="main-navbar">
      <nav class="navbar align-items-center flex-md-nowrap p-0" style="height: 64px; min-height: 64px;">
        <a href="#" class="desktop-toggle-sidebar-action nav-link nav-link-icon text-center border-right d-none d-md-flex align-items-center justify-content-center" style="width: 34px; min-width: 34px; height: 100%; padding: 0;">
          <i class="material-icons" style="font-size: 20px;">&#xE5D2;</i>
        </a>
        <a class="navbar-brand mr-0 d-flex align-items-center justify-content-start" href="{{ url('dashboard') }}" style="line-height: normal; flex-grow: 1; height: 100%; text-decoration: none; padding: 0 1rem; min-width: 0; overflow: hidden;">
          <div class="d-flex align-items-center" style="min-width: 0; width: 100%;">
            <img src="{{ asset('assets/images/silila-icon.png') }}" alt="Logo SILILA" style="height: 38px; width: auto; max-width: 42px; object-fit: contain; margin-right: 9px; flex-shrink: 0; filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));">
            <div class="d-flex flex-column text-left justify-content-center" style="min-width: 0; overflow: hidden;">
              <span style="font-family: var(--font-heading); font-weight: 800; font-size: 16px; color: #ffffff; letter-spacing: 0.3px; line-height: 1.15; white-space: nowrap;">SILILA</span>
              <span style="font-size: 9px; color: #fef08a; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; line-height: 1.25; margin-top: 2px;">Kab. Banyuwangi</span>
            </div>
          </div>
        </a>
        <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none d-flex align-items-center p-3 text-white" style="cursor: pointer; height: 100%;">
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
            <a class="nav-link {{ request()->routeIs('dashboard.permohonan') ? 'active' : '' }}" href="{{ route('dashboard.permohonan') }}">
                <i class="material-icons">description</i>
                <span>Permohonan Surat</span>
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
