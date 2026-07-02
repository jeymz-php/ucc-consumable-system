@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@php
    $firstName = explode(' ', $user->name)[0];
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
@endphp

{{-- Hero Banner --}}
<div class="hero-banner" style="margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting">
            {{ $greeting }}, {{ $firstName }}! 👋
        </div>
        <p class="hero-sub">
            @if(in_array($role, ['admin','superadmin']))
                Full system overview. Monitor consumables, manage requests, and maintain supply control.
            @else
                Browse available consumable items and submit your requests below.
            @endif
        </p>
        <div class="hero-chips">
            <div class="hero-chip"><i class="ti ti-calendar" style="font-size:12px;margin-right:4px;"></i>{{ now()->format('l, F d, Y') }}</div>
            @if(in_array($role, ['admin','superadmin']))
            <div class="hero-chip"><span>Pending Requests</span>{{ $stats['pending_requests'] }}</div>
            @else
            <div class="hero-chip"><span>My Requests</span>{{ $stats['my_requests'] }}</div>
            <div class="hero-chip"><span>Pending</span>{{ $stats['my_pending'] }}</div>
            @endif
        </div>
    </div>
    <div class="hero-right">
        @if($role === 'user')
        <a href="{{ route('consumables') }}" class="btn-add"><i class="ti ti-shopping-cart"></i> Browse Items</a>
        @else
        <a href="{{ route('consumable-requests') }}" class="btn-add"><i class="ti ti-clipboard-list"></i> View Requests</a>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-package"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_items'] }}</div>
            <div class="stat-label">Total Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['available'] }}</div>
            <div class="stat-label">Available Stock</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['low_stock'] }}</div>
            <div class="stat-label">Low Stock</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['critical'] }}</div>
            <div class="stat-label">Critical Stock</div>
        </div>
    </div>
</div>

<div class="two-col">

    {{-- Recent Requests --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-clipboard-list"></i>
                {{ $role === 'user' ? 'My Recent Requests' : 'Recent Requests' }}
            </div>
            <a href="{{ route('consumable-requests') }}" style="font-size:12px; color:var(--blue); font-weight:600; text-decoration:none;">View All →</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($recentRequests as $req)
            <div class="activity-item" style="padding:12px 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; gap:10px;">
                <div style="width:34px; height:34px; border-radius:50%; background:
                    @if($req->status==='approved') #f0faf4
                    @elseif($req->status==='rejected') #fff5f5
                    @elseif($req->status==='partial') #fff8f0
                    @else #eff6ff @endif;
                    display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; color:
                    @if($req->status==='approved') #1a6b3a
                    @elseif($req->status==='rejected') #e24b4a
                    @elseif($req->status==='partial') #ef9f27
                    @else var(--blue) @endif;">
                    <i class="ti ti-{{ $req->status==='approved' ? 'circle-check' : ($req->status==='rejected' ? 'circle-x' : ($req->status==='partial' ? 'circle-half-2' : 'clock')) }}"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $req->reference_no }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ $req->items->count() }} item(s) • {{ $req->created_at->format('M d, Y') }}</div>
                    @if($role !== 'user')
                    <div style="font-size:11.5px; color:var(--text-muted);">by {{ $req->requester->name ?? '—' }}</div>
                    @endif
                </div>
                <span class="chip-badge @if($req->status==='approved') chip-status-active @elseif($req->status==='rejected') chip-status-inactive @elseif($req->status==='partial') chip-campus @else @endif"
                      style="{{ $req->status==='pending' ? 'background:#eff6ff;color:var(--blue);' : '' }}">
                    {{ ucfirst($req->status) }}
                </span>
            </div>
            @empty
            <div class="empty-state" style="padding:2rem;">
                <i class="ti ti-clipboard-off"></i>
                <p>No requests yet.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Critical Items / Top Items --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">

        @if(in_array($role, ['admin','superadmin']) && $criticalItems->count() > 0)
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="color:var(--red);"><i class="ti ti-alert-circle"></i> Critical Stock Alert</div>
                <a href="{{ route('consumables') }}?stock_status=critical" style="font-size:12px; color:var(--red); font-weight:600; text-decoration:none;">View All →</a>
            </div>
            <div class="card-body" style="padding:0;">
                @foreach($criticalItems as $item)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 1.25rem; border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:13px; font-weight:600;">{{ $item->item_name }}</div>
                        <div style="font-size:11.5px; color:var(--text-muted);">{{ $item->category->name ?? 'Uncategorized' }}</div>
                    </div>
                    <span class="chip-badge chip-status-inactive" style="flex-shrink:0;">{{ $item->current_stock }} {{ $item->unit }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="ti ti-trending-up"></i> Top Requested Items</div>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($topItems as $top)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 1.25rem; border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:13px; font-weight:600;">{{ $top->consumable->item_name ?? '—' }}</div>
                        <div style="font-size:11.5px; color:var(--text-muted);">{{ $top->consumable->unit ?? '' }}</div>
                    </div>
                    <span class="chip-badge chip-campus" style="flex-shrink:0;">{{ $top->total_qty }} consumed</span>
                </div>
                @empty
                <div class="empty-state" style="padding:2rem;">
                    <i class="ti ti-chart-bar-off"></i>
                    <p>No consumption data yet.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Quick Actions --}}
<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-bolt"></i> Quick Actions</div></div>
    <div class="card-body">
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
            @if($role === 'user')
            <a href="{{ route('consumables') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-shopping-cart" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Browse Items</span>
            </a>
            <a href="{{ route('consumable-requests') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-clipboard-list" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">My Requests</span>
            </a>
            <a href="{{ route('account.settings') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-settings-2" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Account Settings</span>
            </a>
            @else
            <a href="{{ route('consumables') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-package" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Inventory</span>
            </a>
            <a href="{{ route('consumable-requests') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-clipboard-list" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Requests</span>
            </a>
            <a href="{{ route('consumables.reports') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-chart-bar" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Reports</span>
            </a>
            <a href="{{ route('notifications.index') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-bell" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Notifications</span>
            </a>
            <a href="{{ route('users') }}" style="display:flex; flex-direction:column; align-items:center; gap:8px; padding:1.2rem 1.5rem; border:1.5px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text-primary); transition:all 0.18s; min-width:100px; text-align:center;" onmouseover="this.style.borderColor='var(--blue)';this.style.background='var(--blue-light)'" onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                <i class="ti ti-users" style="font-size:22px; color:var(--blue);"></i>
                <span style="font-size:12px; font-weight:600;">Users</span>
            </a>
            @endif
        </div>
    </div>
</div>

@endsection