@php use Illuminate\Support\Str; @endphp
<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
    <ul class="nav">
        <!-- USER PROFILE -->
        <li class="nav-item nav-profile not-navigation-link">
            <div class="nav-link">
                <div class="user-wrapper">
                    <div class="profile-image">
                        <img src="{{ auth()->user()->avatar ? asset('storage/avatar/' . auth()->user()->avatar) : asset('admin/assets/images/faces/face8.jpg') }}"
                            alt="profile image">
                    </div>
                    <div class="text-wrapper">
                        <p class="profile-name">{{ auth()->user()->name }}</p>
                        <small class="designation text-muted">
                            {{-- {{ auth()->user()->roles->pluck('name_roles')->first() ?? 'User' }} --}}
                        </small>
                    </div>
                </div>
            </div>
        </li>

        {{-- MENU UNTUK ADMIN --}}

        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/dashboard">
                <i class="menu-icon mdi mdi-view-dashboard"></i>
                <span class="menu-title">DASHBOARD</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/users*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="admin/device">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">DATA DEVICE</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/fields*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/admin/profile">
                <i class="menu-icon mdi mdi-soccer-field"></i>
                <span class="menu-title">DATA PROFILE</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/orders*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/auto-reply">
                <i class="menu-icon mdi mdi-cart"></i>
                <span class="menu-title">DATA PESAN OTOMATIS</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/log*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/admin/log">
                <i class="menu-icon mdi mdi-tools"></i>
                <span class="menu-title">DATA LOG SERVER</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/field_types*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/admin/field_types">
                <i class="menu-icon mdi mdi-view-list"></i>
                <span class="menu-title">DATA TIPE LAPANGAN</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('vendor/diskon*') ? 'active' : '' }}">
            <a class="nav-link" href="/">
                <i class="fas fa-tags"></i>
                <span>Manajemen Diskon</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/withdraws') ? 'active' : '' }}" href="/">
                <i class="menu-icon mdi mdi-cash"></i>
                <span class="menu-title">DATA WITHDRAW</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/settings*') ? 'active bg-gradient-success text-primary' : '' }}"
                href="/">
                <i class="menu-icon mdi mdi-settings"></i>
                <span class="menu-title">DATA SETING</span>
            </a>
        </li>




    </ul>
</nav>
