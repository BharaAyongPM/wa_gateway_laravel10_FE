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
@auth
    @if(auth()->user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active bg-gradient-success text-primary' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="menu-icon mdi mdi-view-dashboard"></i>
                <span class="menu-title">DASHBOARD</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/users*') ? 'active bg-gradient-success text-primary' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">DATA USER</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/perangkat*') ? 'active bg-gradient-success text-primary' : '' }}"
               href="{{ route('admin.perangkat') }}">
                <i class="menu-icon mdi mdi-cellphone"></i>
                <span class="menu-title">DATA ALL DEVICE</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/perangkat*') ? 'active bg-gradient-success text-primary' : '' }}"
               href="{{ route('device.index') }}">
                <i class="menu-icon mdi mdi-cellphone"></i>
                <span class="menu-title">DEVICE ADMIN</span>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link {{ request()->is('messages') ? 'active bg-gradient-success text-primary' : '' }}"
       href="{{ route('messages.index') }}">
        <i class="menu-icon mdi mdi-message-text"></i>
        <span class="menu-title">MESSAGE</span>
    </a>
    
</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/plans*') ? 'active bg-gradient-success text-primary' : '' }}"
               href="{{ route('admin.plans.index') }}">
                <i class="menu-icon mdi mdi-package-variant-closed"></i>
                <span class="menu-title">PLANS</span>
            </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('admin/payments*') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('admin.payments.index') }}">
            <i class="menu-icon mdi mdi-square-inc-cash"></i>
            <span class="menu-title">BILLING / PAYMENTS</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('admin/messages*') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('admin.messages.index') }}">
            <i class="menu-icon mdi mdi-message"></i>
            <span class="menu-title">REKAP & HISTORY PESAN</span>
          </a>
        </li>
        {{-- AUTO REPLY RULES --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->is('admin/autoreplies*') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('admin.autoreplies.index') }}">
            <i class="menu-icon mdi mdi-message-reply-text"></i>
            <span class="menu-title">AUTO REPLY</span>
          </a>
        </li>

        {{-- BROADCAST SCHEDULER --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->is('admin/broadcasts*') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('admin.broadcasts.index') }}">
            <i class="menu-icon mdi mdi-message-settings-variant"></i>
            <span class="menu-title">BROADCAST</span>
          </a>
        </li>
        <li class="nav-item">
  <a class="nav-link {{ request()->routeIs('bot.settings') ? 'active bg-gradient-success text-primary' : '' }}"
     href="{{ route('bot.settings') }}">
    <i class="menu-icon mdi mdi-robot"></i>
    <span class="menu-title">Bot Otomatis</span>
  </a>
</li>
    @endif

    {{-- MENU UNTUK USER --}}
    @if(auth()->user()->role === 'user')
        <li class="nav-item">
          <a class="nav-link {{ request()->is('user/dashboard') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('user.dashboard') }}">
            <i class="menu-icon mdi mdi-view-dashboard"></i>
            <span class="menu-title">Dashboard</span>
          </a>
        </li>
         <li class="nav-item">
          <a class="nav-link {{ request()->is('device') ? 'active bg-gradient-success text-primary' : '' }}"
             href="{{ route('device.index') }}">
            <i class="menu-icon mdi mdi-view-dashboard"></i>
            <span class="menu-title">DEVICE</span>
          </a>
        </li>
        <li class="nav-item">
    <a class="nav-link {{ request()->is('messages') ? 'active bg-gradient-success text-primary' : '' }}"
       href="{{ route('messages.index') }}">
        <i class="menu-icon mdi mdi-message-text"></i>
        <span class="menu-title">MESSAGE</span>
    </a>
    
</li>
<li class="nav-item">
  <a class="nav-link {{ request()->is('user/my/messages') ? 'active bg-gradient-success text-primary' : '' }}"
     href="{{ route('user.messages.history') }}">
    <i class="menu-icon mdi mdi-message-text-clock"></i>
    <span class="menu-title">History Pesan</span>
  </a>
</li>
<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('bot.settings') ? 'active bg-gradient-success text-primary' : '' }}"
     href="{{ route('bot.settings') }}">
    <i class="menu-icon mdi mdi-robot"></i>
    <span class="menu-title">Bot Otomatis</span>
  </a>
</li>


    @endif
@endauth


    </ul>
</nav>
{{-- API KEYS & WEBHOOKS --}}
 {{-- <li class="nav-item">
//   <a class="nav-link {{ request()->is('admin/integrations*') ? 'active bg-gradient-success text-primary' : '' }}"
//      href="{{ route('admin.integrations.index') }}">
//     <i class="menu-icon fas fa-plug"></i>
//     <span class="menu-title">API & WEBHOOKS</span>
//   </a>
// </li> --}}

{{-- CONTACTS & GROUPS --}}
 {{-- <li class="nav-item">
//   <a class="nav-link {{ request()->is('admin/directory*') ? 'active bg-gradient-success text-primary' : '' }}"
//      href="{{ route('admin.directory.index') }}">
//     <i class="menu-icon fas fa-address-book"></i>
//     <span class="menu-title">CONTACTS / GROUPS</span>
//   </a>
// </li> --}}

 {{-- AUDIT LOG --}}
 {{-- <li class="nav-item">
//   <a class="nav-link {{ request()->is('admin/audit*') ? 'active bg-gradient-success text-primary' : '' }}"
//      href="{{ route('admin.audit.index') }}">
//     <i class="menu-icon fas fa-clipboard-list"></i>
//     <span class="menu-title">AUDIT LOG</span>
//   </a>
// </li> --}}





        {{-- <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/users*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="admin/device">
        //         <i class="menu-icon mdi mdi-account-multiple"></i>
        //         <span class="menu-title">DATA DEVICE</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/fields*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="/admin/profile">
        //         <i class="menu-icon mdi mdi-soccer-field"></i>
        //         <span class="menu-title">DATA PROFILE</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/orders*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="/auto-reply">
        //         <i class="menu-icon mdi mdi-cart"></i>
        //         <span class="menu-title">DATA PESAN OTOMATIS</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/log*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="/log">
        //         <i class="menu-icon mdi mdi-tools"></i>
        //         <span class="menu-title">DATA LOG SERVER</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/field_types*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="/broadcasts">
        //         <i class="menu-icon mdi mdi-view-list"></i>
        //         <span class="menu-title">DATA BROADCAST</span>
        //     </a>
        // </li>
        // <li class="nav-item {{ request()->is('vendor/diskon*') ? 'active' : '' }}">
        //     <a class="nav-link" href="/">
        //         <i class="fas fa-tags"></i>
        //         <span>Manajemen Diskon</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/withdraws') ? 'active' : '' }}" href="/">
        //         <i class="menu-icon mdi mdi-cash"></i>
        //         <span class="menu-title">DATA WITHDRAW</span>
        //     </a>
        // </li>
        // <li class="nav-item">
        //     <a class="nav-link {{ request()->is('admin/settings*') ? 'active bg-gradient-success text-primary' : '' }}"
        //         href="/">
        //         <i class="menu-icon mdi mdi-settings"></i>
        //         <span class="menu-title">DATA SETING</span>
        //     </a>
        // </li> --}}
