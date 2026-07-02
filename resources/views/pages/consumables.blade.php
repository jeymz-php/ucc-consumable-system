@extends('layouts.app')
@section('title', 'Browse Consumables')
@section('page-title', 'Browse Consumables')

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="ti ti-circle-check"></i><div class="alert-text"><strong>Success</strong>{{ session('success') }}</div></div>
@endif
@if($errors->any())
<div class="alert alert-error"><i class="ti ti-alert-circle"></i><div class="alert-text"><strong>Request Failed</strong><ul style="margin:4px 0 0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
@endif

@php
    $firstName = explode(' ', auth()->user()->name)[0];
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
@endphp

{{-- Hero --}}
<div class="hero-banner" style="margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-package"></i> {{ $greeting }}, {{ $firstName }}!</div>
        <p class="hero-sub">Browse available consumable items and add them to your request cart.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total</span>{{ $stats['total'] }}</div>
            <div class="hero-chip"><span>Available</span>{{ $stats['available'] }}</div>
            <div class="hero-chip"><span>Low</span>{{ $stats['low'] }}</div>
            <div class="hero-chip"><span>Critical</span>{{ $stats['critical'] }}</div>
        </div>
    </div>
    <div class="hero-right">
        <a href="{{ route('consumable-requests') }}" class="btn-add"><i class="ti ti-clipboard-list"></i> My Requests</a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--green-light); color:var(--green-dark);"><i class="ti ti-package"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Items</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $stats['low'] }}</div><div class="stat-label">Low Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div><div class="stat-value">{{ $stats['critical'] }}</div><div class="stat-label">Critical</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f5f5; color:#999;"><i class="ti ti-ban"></i></div>
        <div><div class="stat-value">{{ $stats['out_of_stock'] }}</div><div class="stat-label">Out of Stock</div></div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <div style="position:relative; margin-bottom:1rem;">
            <i class="ti ti-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
            <input type="text" id="cs-search" placeholder="Search by name, brand, or category..."
                   style="width:100%; padding:12px 16px 12px 42px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; font-family:inherit; outline:none; transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='var(--green-dark)'" onblur="this.style.borderColor='var(--border)'">
        </div>
        <div class="filter-pills">
            <button type="button" class="filter-pill cs-stock-pill active" data-value="all">All</button>
            <button type="button" class="filter-pill cs-stock-pill" data-value="available">Available</button>
            <button type="button" class="filter-pill cs-stock-pill" data-value="low">Low Stock</button>
            <button type="button" class="filter-pill cs-stock-pill" data-value="critical">Critical</button>
            <button type="button" class="filter-pill cs-stock-pill" data-value="out">Out of Stock</button>
        </div>
    </div>
</div>

{{-- Items Grid --}}
<div class="items-grid" id="cs-items-grid">
    @forelse($items as $item)
    @php
        $isOut  = $item->current_stock <= 0;
        $status = $isOut ? 'out' : $item->status;
        $statusLabel = $isOut ? 'Out of Stock' : ucfirst($item->status);
    @endphp
    <div class="item-card status-{{ $status }}"
         data-name="{{ strtolower($item->item_name) }}"
         data-brand="{{ strtolower($item->brand ?? '') }}"
         data-category="{{ strtolower($item->category->name ?? '') }}"
         data-status="{{ $status }}">

        <div class="item-card-top">
            <span class="item-card-category">
                <i class="ti ti-tag" style="font-size:10px;"></i>
                {{ $item->category->name ?? 'Uncategorized' }}
            </span>
            <span class="item-status-badge {{ $status }}">
                <i class="ti ti-{{ $isOut ? 'ban' : ($status === 'critical' ? 'alert-circle' : ($status === 'low' ? 'alert-triangle' : 'circle-check')) }}" style="font-size:10px;"></i>
                {{ $statusLabel }}
            </span>
        </div>

        <div class="item-card-name">{{ $item->item_name }}</div>
        <div class="item-card-brand">
            @if($item->brand)
                <i class="ti ti-bookmark" style="font-size:11px;"></i> {{ $item->brand }}
            @else
                &nbsp;
            @endif
        </div>

        <div class="item-card-meta">
            <div>
                <div class="item-card-meta-label">Stock</div>
                <div class="item-card-meta-value">{{ $item->current_stock }} <span style="font-size:11px; font-weight:400; color:#888;">{{ $item->unit }}</span></div>
            </div>
            <div style="text-align:right;">
                <div class="item-card-meta-label">Item ID</div>
                <div class="item-card-meta-value" style="font-size:13px;">#{{ $item->id }}</div>
            </div>
        </div>

        @if(!$isOut)
        <button class="item-card-btn can-request" id="cart-btn-{{ $item->id }}"
                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ $item->unit }}', {{ $item->current_stock }}, '{{ $status }}')">
            <i class="ti ti-shopping-cart-plus"></i> Add to Request
        </button>
        @else
        <button class="item-card-btn cannot-request" disabled>
            <i class="ti ti-ban"></i> Out of Stock
        </button>
        @endif
    </div>
    @empty
    <div style="grid-column:1/-1;">
        <div class="empty-state"><i class="ti ti-package-off"></i><p>No consumable items found.</p></div>
    </div>
    @endforelse
</div>

<div class="empty-state" id="cs-no-results" style="display:none;">
    <i class="ti ti-search-off"></i><p>No items match your search.</p>
</div>

{{-- Floating Cart --}}
<button class="floating-cart" id="floating-cart" onclick="openCartModal()" style="display:none;">
    <i class="ti ti-shopping-cart"></i>
    <span class="floating-cart-badge" id="cart-badge">0</span>
</button>

{{-- CART MODAL --}}
<div class="modal-overlay" id="cart-modal">
    <div class="modal-box-lg" style="max-width:640px;">
        <div class="modal-header-row" style="background:var(--green-dark); margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-shopping-cart"></i> My Request Cart</div>
            <button class="modal-close" onclick="document.getElementById('cart-modal').classList.remove('open');" style="background:rgba(255,255,255,0.15); color:#fff; border-color:transparent;"><i class="ti ti-x"></i></button>
        </div>

        {{-- User Info --}}
        <div class="detail-section" style="margin-bottom:1rem;">
            <div class="detail-section-title"><i class="ti ti-user"></i> Your Information</div>
            <div class="detail-grid" style="margin-top:0.75rem;">
                <div class="detail-row"><span>Full Name</span><strong>{{ auth()->user()->name }}</strong></div>
                <div class="detail-row"><span>Department</span><strong>{{ auth()->user()->department->department_name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Campus</span><strong>{{ auth()->user()->campus->name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Request Date</span><strong>{{ now()->format('M d, Y') }}</strong></div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div id="cart-items-list" style="margin-bottom:1rem;"></div>

        {{-- Signatories --}}
        <div class="detail-section" style="margin-bottom:1rem;">
            <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
            <div class="detail-grid" style="margin-top:0.75rem;">
                <div class="detail-row"><span>Approved By</span><strong>REYNALDO H. CARANDANG JR.</strong></div>
                <div class="detail-row"><span>Supply Officer</span><strong>MARVIN Z. GERVACIO</strong></div>
            </div>
        </div>

        <form method="POST" action="{{ route('consumable-requests.store') }}" id="cart-form">
            @csrf
            <div id="cart-hidden-inputs"></div>
            <button type="submit" class="modal-btn-primary" id="cart-submit-btn" style="background:var(--green-dark);">
                <i class="ti ti-send"></i> Submit Request
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let cart = {};
const CART_KEY = 'cs_consumable_cart_{{ auth()->id() }}';

function saveCart()   { localStorage.setItem(CART_KEY, JSON.stringify(cart)); }
function loadCart()   { try { const s = localStorage.getItem(CART_KEY); if (s) cart = JSON.parse(s) || {}; } catch(e) { cart = {}; } }

function updateCartUI() {
    saveCart();
    const count = Object.keys(cart).length;
    const btn   = document.getElementById('floating-cart');
    document.getElementById('cart-badge').textContent = count;
    btn.style.display = count > 0 ? 'flex' : 'none';
}

function addToCart(id, name, unit, max, status) {
    if (cart[id]) {
        if (status !== 'critical' && cart[id].qty < max) cart[id].qty++;
    } else {
        cart[id] = { name, unit, max, qty: 1, purpose: '', status };
    }
    updateCartUI();

    const btn = document.getElementById(`cart-btn-${id}`);
    if (btn) {
        btn.innerHTML = `<i class="ti ti-check"></i> Added!`;
        btn.classList.remove('can-request');
        btn.classList.add('in-cart');
        setTimeout(() => {
            btn.innerHTML = `<i class="ti ti-shopping-cart-plus"></i> In Cart (${cart[id].qty})`;
        }, 700);
    }
}

function openCartModal() {
    const list = document.getElementById('cart-items-list');
    const submitBtn = document.getElementById('cart-submit-btn');
    const entries = Object.entries(cart);

    if (entries.length === 0) {
        list.innerHTML = `<div class="empty-state"><i class="ti ti-shopping-cart-off"></i><p>Your cart is empty. Add items from the grid.</p></div>`;
        submitBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
        list.innerHTML = `
            <div class="detail-section-title" style="margin-bottom:0.75rem;"><i class="ti ti-list"></i> Requested Items (${entries.length})</div>
            ${entries.map(([id, item]) => {
                const locked = item.status === 'critical' || item.status === 'out';
                return `
                <div class="cart-row">
                    <div style="display:flex; align-items:center; gap:10px; width:100%; flex-wrap:wrap;">
                        <div class="cart-row-name" style="flex:1; min-width:120px;">${item.name}</div>
                        <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                            <input type="number" class="cart-qty-input" min="1" max="${item.max}"
                                   value="${item.qty}" ${locked ? 'disabled' : ''}
                                   onchange="updateQty(${id}, this.value)">
                            <span style="font-size:11.5px; color:#888;">${item.unit}</span>
                            <button type="button" class="cart-remove-btn" onclick="removeFromCart(${id})" title="Remove">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                    ${locked ? `<div style="width:100%; font-size:11px; color:#c2410c; margin-top:4px;"><i class="ti ti-lock" style="font-size:11px;"></i> Quantity locked for critical/out-of-stock items.</div>` : ''}
                    <input type="text" class="modal-input" placeholder="Purpose (e.g. Office use, Lab use...)"
                           style="width:100%; font-size:12.5px; margin-top:6px;"
                           value="${item.purpose || ''}"
                           oninput="cart[${id}].purpose = this.value; syncHiddenInputs();">
                </div>`;
            }).join('')}
        `;
        syncHiddenInputs();
    }
    document.getElementById('cart-modal').classList.add('open');
}

function updateQty(id, val) {
    if (!cart[id]) return;
    val = Math.max(1, Math.min(parseInt(val) || 1, cart[id].max));
    cart[id].qty = val;
    updateCartUI();
    openCartModal();
}

function removeFromCart(id) {
    delete cart[id];
    // Reset button on grid
    const btn = document.getElementById(`cart-btn-${id}`);
    if (btn) { btn.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Add to Request'; btn.className = 'item-card-btn can-request'; }
    updateCartUI();
    openCartModal();
}

function syncHiddenInputs() {
    document.getElementById('cart-hidden-inputs').innerHTML = Object.entries(cart).map(([id, item], idx) => `
        <input type="hidden" name="items[${idx}][consumable_id]" value="${id}">
        <input type="hidden" name="items[${idx}][quantity]"      value="${item.qty}">
        <input type="hidden" name="items[${idx}][purpose]"       value="${(item.purpose || '').replace(/"/g, '&quot;')}">
    `).join('');
}

document.getElementById('cart-form').addEventListener('submit', function() {
    syncHiddenInputs();
    // Validate purposes
    for (const [id, item] of Object.entries(cart)) {
        if (!item.purpose || !item.purpose.trim()) {
            alert(`Please enter a purpose for: ${item.name}`);
            event.preventDefault();
            openCartModal();
            return;
        }
    }
    localStorage.removeItem(CART_KEY);
});

// Search & filter
document.getElementById('cs-search').addEventListener('input', filterItems);
document.querySelectorAll('.cs-stock-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        document.querySelectorAll('.cs-stock-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        filterItems();
    });
});

function filterItems() {
    const q      = document.getElementById('cs-search').value.toLowerCase();
    const active = document.querySelector('.cs-stock-pill.active').dataset.value;
    let count    = 0;
    document.querySelectorAll('.item-card').forEach(card => {
        const matchSearch = !q || card.dataset.name.includes(q) || card.dataset.brand.includes(q) || card.dataset.category.includes(q);
        const matchFilter = active === 'all' || card.dataset.status === active;
        const show = matchSearch && matchFilter;
        card.style.display = show ? 'flex' : 'none';
        if (show) count++;
    });
    document.getElementById('cs-no-results').style.display = count === 0 ? 'block' : 'none';
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// Init
loadCart();
updateCartUI();
// Restore in-cart state on card buttons
Object.keys(cart).forEach(id => {
    const btn = document.getElementById(`cart-btn-${id}`);
    if (btn) { btn.innerHTML = `<i class="ti ti-shopping-cart-plus"></i> In Cart (${cart[id].qty})`; btn.classList.add('in-cart'); btn.classList.remove('can-request'); }
});
</script>
@endpush