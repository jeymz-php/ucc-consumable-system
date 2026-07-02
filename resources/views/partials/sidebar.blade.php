@php $role = auth()->user()->role; @endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="{{ asset('images/ucc.png') }}" alt="UCC"
                 onerror="this.parentElement.style.background='var(--green-dark)'; this.style.display='none'; this.parentElement.innerHTML+='<span style=\'font-size:14px;font-weight:700;color:#fff;\'>UCC</span>'">
        </div>
        <div>
            <div class="brand-text-main">UCC-CS</div>
            <div class="brand-text-sub">Consumable System</div>
        </div>
    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div style="min-width:0;">
            <div class="user-info-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ auth()->user()->name }}
            </div>
            @if($role === 'superadmin')
                <span class="user-info-role role-superadmin">Super Admin</span>
            @elseif($role === 'admin')
                <span class="user-info-role role-admin">Admin</span>
            @else
                <span class="user-info-role role-user">User</span>
            @endif
            @if(auth()->user()->campus)
            <div class="user-campus">
                <i class="ti ti-map-pin" style="font-size:9px;"></i>
                {{ auth()->user()->campus->name }}
            </div>
            @endif
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        <div class="nav-section-label">Consumables</div>

        @if($role === 'user')
        <a href="{{ route('consumables') }}"
           class="nav-item {{ request()->routeIs('consumables') ? 'active' : '' }}">
            <i class="ti ti-shopping-cart"></i> Browse Items
        </a>
        <a href="{{ route('consumable-requests') }}"
           class="nav-item {{ request()->routeIs('consumable-requests*') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i> My Requests
        </a>
        @else
        <a href="{{ route('consumables') }}"
           class="nav-item {{ request()->routeIs('consumables') ? 'active' : '' }}">
            <i class="ti ti-package"></i> Inventory
        </a>
        <a href="{{ route('consumable-requests') }}"
           class="nav-item {{ request()->routeIs('consumable-requests*') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i> Request History
        </a>
        <a href="{{ route('consumables.reports') }}"
           class="nav-item {{ request()->routeIs('consumables.reports*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar"></i> Reports
        </a>
        @endif

        @if(in_array($role, ['admin', 'superadmin']))
        <div class="nav-section-label">Management</div>
        <a href="{{ route('notifications.index') }}"
           class="nav-item {{ request()->routeIs('notifications*') ? 'active' : '' }}">
            <i class="ti ti-bell"></i> Notifications
        </a>
        <a href="{{ route('users') }}"
           class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
            <i class="ti ti-users"></i> Users
        </a>
        @if($role === 'superadmin')
        <a href="{{ route('system.settings') }}"
           class="nav-item {{ request()->routeIs('system*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i> System Settings
        </a>
        @endif
        @endif

        <div class="nav-section-label">Account</div>
        <a href="{{ route('account.settings') }}"
           class="nav-item {{ request()->routeIs('account*') ? 'active' : '' }}">
            <i class="ti ti-settings-2"></i> Account Settings
        </a>

    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item logout"
                    style="width:100%; border:none; background:none; cursor:pointer; font-family:inherit; text-align:left;">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>
    </div>

</aside>