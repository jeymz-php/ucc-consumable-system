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
            onclick="window.ConsumableCart.addToCart({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ $item->unit }}', {{ $item->current_stock }}, '{{ $status }}')">
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
<button class="floating-cart" id="floating-cart" onclick="window.ConsumableCart.openCartModal()" style="display:none;">
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

@push('styles')
<style>
/* ── Item Card Grid ── */
.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.item-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 8px;
    box-shadow: var(--card-shadow);
    transition: transform 0.15s, box-shadow 0.15s;
}
.item-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }

.item-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-wrap: wrap;
}

.item-card-category {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.item-status-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.item-status-badge.available { background: #f0faf4; color: var(--green-dark); }
.item-status-badge.low       { background: #fff8f0; color: #ef9f27; }
.item-status-badge.critical  { background: #fff5f5; color: var(--red); }
.item-status-badge.out       { background: #f5f5f5; color: #999; }

.item-card-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.3;
    flex: 1;
}

.item-card-brand {
    font-size: 11.5px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.item-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 8px 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin: 4px 0;
}
.item-card-meta-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
.item-card-meta-value { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-top: 2px; }

.item-card-btn {
    width: 100%;
    padding: 9px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    transition: opacity 0.18s, background 0.18s;
    margin-top: auto;
}
.item-card-btn.can-request  { background: var(--green-dark); color: #fff; }
.item-card-btn.can-request:hover { opacity: 0.88; }
.item-card-btn.in-cart      { background: #f0faf4; color: var(--green-dark); border: 1.5px solid var(--green-dark); }
.item-card-btn.cannot-request { background: #f5f5f5; color: #aaa; cursor: not-allowed; }

/* ── Floating Cart Button ── */
.floating-cart {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: var(--green-dark);
    color: #fff;
    border: none;
    font-size: 22px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 24px rgba(0,0,0,0.18);
    z-index: 200;
    transition: transform 0.18s;
}
.floating-cart:hover { transform: scale(1.08); }

.floating-cart-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--red);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ── Cart Modal Rows ── */
.cart-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.cart-row:last-child { border-bottom: none; }
.cart-row-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }

.cart-qty-input {
    width: 70px;
    padding: 6px 10px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    text-align: center;
    outline: none;
}
.cart-qty-input:focus { border-color: var(--green-dark); }

.cart-remove-btn {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: none;
    background: #fff5f5;
    color: var(--red);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: opacity 0.15s;
}
.cart-remove-btn:hover { opacity: 0.75; }

@media (max-width: 600px) {
    .items-grid { grid-template-columns: 1fr 1fr; }
    .floating-cart { bottom: 1.25rem; right: 1.25rem; }
}
@media (max-width: 400px) {
    .items-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
// Use a unique namespace to avoid conflicts
window.ConsumableCart = (function() {
    // Private variables
    let cart = {};
    const CART_KEY = 'cs_consumable_cart_{{ auth()->id() }}';

    // Private functions
    function saveCart() { 
        try {
            localStorage.setItem(CART_KEY, JSON.stringify(cart)); 
        } catch(e) {
            console.warn('Could not save cart:', e);
        }
    }
    
    function loadCart() { 
        try { 
            const s = localStorage.getItem(CART_KEY); 
            if (s) cart = JSON.parse(s) || {}; 
        } catch(e) { 
            cart = {}; 
        }
    }

    function updateCartUI() {
        saveCart();
        const count = Object.keys(cart).length;
        const btn = document.getElementById('floating-cart');
        const badge = document.getElementById('cart-badge');
        
        if (btn) {
            btn.style.display = count > 0 ? 'flex' : 'none';
        }
        if (badge) {
            badge.textContent = count;
        }
    }

    // Public functions
    function addToCart(id, name, unit, max, status) {
        if (!id) return;
        
        if (cart[id]) {
            if (status !== 'out' && cart[id].qty < max) {
                cart[id].qty++;
            }
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

        if (!list) return;

        if (entries.length === 0) {
            list.innerHTML = `<div class="empty-state"><i class="ti ti-shopping-cart-off"></i><p>Your cart is empty. Add items from the grid.</p></div>`;
            if (submitBtn) submitBtn.disabled = true;
        } else {
            if (submitBtn) submitBtn.disabled = false;
            list.innerHTML = `
                <div class="detail-section-title" style="margin-bottom:0.75rem;"><i class="ti ti-list"></i> Requested Items (${entries.length})</div>
                ${entries.map(([id, item]) => {
                    const locked = item.status === 'out';
                    return `
                    <div class="cart-row">
                        <div style="display:flex; align-items:center; gap:10px; width:100%; flex-wrap:wrap;">
                            <div class="cart-row-name" style="flex:1; min-width:120px;">${item.name}</div>
                            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                                <input type="number" class="cart-qty-input" min="1" max="${item.max}"
                                       value="${item.qty}" ${locked ? 'disabled' : ''}
                                       onchange="window.ConsumableCart.updateQty(${id}, this.value)">
                                <span style="font-size:11.5px; color:#888;">${item.unit}</span>
                                <button type="button" class="cart-remove-btn" onclick="window.ConsumableCart.removeFromCart(${id})" title="Remove">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                        ${item.status === 'critical' ? `<div style="width:100%; font-size:11px; color:#c2410c; margin-top:4px;"><i class="ti ti-alert-triangle" style="font-size:11px;"></i> Critical stock — limited quantity available (max ${item.max}).</div>` : ''}
                        ${locked ? `<div style="width:100%; font-size:11px; color:#c2410c; margin-top:4px;"><i class="ti ti-lock" style="font-size:11px;"></i> Quantity locked for out-of-stock items.</div>` : ''}
                        <input type="text" class="modal-input" placeholder="Purpose (e.g. Office use, Lab use...)"
                               style="width:100%; font-size:12.5px; margin-top:6px;"
                               value="${item.purpose || ''}"
                               oninput="window.ConsumableCart.updatePurpose(${id}, this.value)">
                    </div>`;
                }).join('')}
            `;
            syncHiddenInputs();
        }
        
        const modal = document.getElementById('cart-modal');
        if (modal) modal.classList.add('open');
    }

    function updateQty(id, val) {
        if (!cart[id]) return;
        val = Math.max(1, Math.min(parseInt(val) || 1, cart[id].max));
        cart[id].qty = val;
        updateCartUI();
        openCartModal();
    }

    function updatePurpose(id, value) {
        if (cart[id]) {
            cart[id].purpose = value;
            syncHiddenInputs();
        }
    }

    function removeFromCart(id) {
        delete cart[id];
        const btn = document.getElementById(`cart-btn-${id}`);
        if (btn) { 
            btn.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Add to Request'; 
            btn.className = 'item-card-btn can-request'; 
        }
        updateCartUI();
        openCartModal();
    }

    function syncHiddenInputs() {
        const container = document.getElementById('cart-hidden-inputs');
        if (!container) return;
        
        container.innerHTML = Object.entries(cart).map(([id, item], idx) => `
            <input type="hidden" name="items[${idx}][consumable_id]" value="${id}">
            <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
            <input type="hidden" name="items[${idx}][purpose]" value="${(item.purpose || '').replace(/"/g, '&quot;')}">
        `).join('');
    }

    function filterItems() {
        const searchEl = document.getElementById('cs-search');
        const activePill = document.querySelector('.cs-stock-pill.active');
        
        if (!searchEl || !activePill) {
            console.log('Search elements not found');
            return;
        }

        const q = searchEl.value.trim().toLowerCase();
        const active = activePill.dataset.value;
        let count = 0;

        const cards = document.querySelectorAll('#cs-items-grid .item-card');

        cards.forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const brand = (card.dataset.brand || '').toLowerCase();
            const category = (card.dataset.category || '').toLowerCase();
            const status = card.dataset.status || '';

            const matchSearch = !q || name.includes(q) || brand.includes(q) || category.includes(q);
            // "All" never includes out-of-stock items — those only show up
            // when the "Out of Stock" pill is explicitly selected.
            const matchFilter = active === 'out'
                ? status === 'out'
                : (active === 'all' ? status !== 'out' : status === active);

            const show = matchSearch && matchFilter;
            card.style.display = show ? '' : 'none';
            if (show) count++;
        });

        const noResults = document.getElementById('cs-no-results');
        if (noResults) {
            noResults.style.display = count === 0 ? 'block' : 'none';
        }
    }

    // Initialize function
    function init() {
        loadCart();
        updateCartUI();

        // Update cart buttons for items already in cart
        Object.keys(cart).forEach(id => {
            const btn = document.getElementById(`cart-btn-${id}`);
            if (btn) {
                btn.innerHTML = `<i class="ti ti-shopping-cart-plus"></i> In Cart (${cart[id].qty})`;
                btn.classList.add('in-cart');
                btn.classList.remove('can-request');
            }
        });

        // Setup search
        const searchEl = document.getElementById('cs-search');
        if (searchEl) {
            searchEl.addEventListener('input', filterItems);
        }

        // Setup filter pills
        const pillsWrap = document.querySelector('.filter-pills');
        if (pillsWrap) {
            pillsWrap.addEventListener('click', function(e) {
                const pill = e.target.closest('.cs-stock-pill');
                if (!pill) return;
                
                document.querySelectorAll('.cs-stock-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                filterItems();
            });
        }

        // Setup modal close
        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', function(e) { 
                if (e.target === this) this.classList.remove('open'); 
            });
        });

        // Setup form submit
        const form = document.getElementById('cart-form');
        if (form) {
            form.addEventListener('submit', function(event) {
                syncHiddenInputs();
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
        }

        // Initial filter
        filterItems();
    }

    // Return public API
    return {
        addToCart: addToCart,
        openCartModal: openCartModal,
        updateQty: updateQty,
        updatePurpose: updatePurpose,
        removeFromCart: removeFromCart,
        init: init,
        filterItems: filterItems
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.ConsumableCart.init();
    });
} else {
    window.ConsumableCart.init();
}
</script>
@endpush