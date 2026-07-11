@extends('layouts.app')
@section('title', 'My Requests')
@section('page-title', 'My Requests')

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="ti ti-circle-check"></i><div class="alert-text"><strong>Success</strong>{{ session('success') }}</div></div>
@endif

{{-- Hero --}}
<div class="hero-banner" style="margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-clipboard-list"></i> My Requests</div>
        <p class="hero-sub">Track the status of all your submitted consumable requests.</p>
    </div>
    <div class="hero-right">
        <a href="{{ route('consumables') }}" class="btn-add"><i class="ti ti-shopping-cart"></i> Browse Items</a>
    </div>
</div>

{{-- Filter Pills --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <div class="filter-pills">
            <a href="{{ route('consumable-requests', ['status'=>'all']) }}"     class="filter-pill {{ $status==='all'     ? 'active' : '' }}" style="text-decoration:none;">All</a>
            <a href="{{ route('consumable-requests', ['status'=>'pending']) }}"  class="filter-pill {{ $status==='pending'  ? 'active' : '' }}" style="text-decoration:none;">Pending</a>
            <a href="{{ route('consumable-requests', ['status'=>'approved']) }}" class="filter-pill {{ $status==='approved' ? 'active' : '' }}" style="text-decoration:none;">Approved</a>
            <a href="{{ route('consumable-requests', ['status'=>'partial']) }}"  class="filter-pill {{ $status==='partial'  ? 'active' : '' }}" style="text-decoration:none;">Partial</a>
            <a href="{{ route('consumable-requests', ['status'=>'rejected']) }}" class="filter-pill {{ $status==='rejected' ? 'active' : '' }}" style="text-decoration:none;">Rejected</a>
        </div>
    </div>
</div>

{{-- Requests Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-clipboard-list"></i> My Requests ({{ $requests->total() }})</div>
    </div>

    {{-- Mobile card view --}}
    <div class="mobile-request-list" id="mobile-list">
        @forelse($requests as $req)
        <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                <div>
                    <div style="font-size:13px; font-weight:700; color:var(--text-primary);">{{ $req->reference_no }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ $req->request_date->format('M d, Y') }} • {{ $req->items->count() }} item(s)</div>
                </div>
                @if($req->status==='pending')
                    <span class="chip-badge" style="background:#fff8f0;color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                @elseif($req->status==='approved')
                    <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Approved</span>
                @elseif($req->status==='partial')
                    <span class="chip-badge chip-campus"><i class="ti ti-circle-half-2" style="font-size:10px"></i> Partial</span>
                @else
                    <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                @endif
            </div>
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Reviewed by: {{ $req->reviewer->name ?? 'Not yet reviewed' }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">
                Approved Date: {{ $req->reviewed_at ? $req->reviewed_at->format('M d, Y h:i A') : '—' }}
            </div>
            <div class="table-actions">
                <button class="table-icon-btn view" onclick="viewRequestDetails({{ $req->id }})" title="View Details">
                    <i class="ti ti-eye"></i>
                </button>
                @if(in_array($req->status, ['approved','partial']))
                <a href="{{ route('consumable-requests.report', $req->id) }}" target="_blank" class="table-icon-btn" style="background:#f4f0ff;color:#7c3aed;" title="Generate Report">
                    <i class="ti ti-file-text"></i>
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:2.5rem 1rem;">
            <i class="ti ti-clipboard-off"></i>
            <p>No requests yet. <a href="{{ route('consumables') }}" style="color:var(--green-dark); font-weight:600;">Browse items →</a></p>
        </div>
        @endforelse
    </div>

    {{-- Desktop table view --}}
    <div class="data-table-wrap desktop-table">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Reviewed By</th>
                    <th>Approved Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td style="font-weight:600; font-size:12.5px;">{{ $req->reference_no }}</td>
                    <td style="font-size:12px;">{{ $req->request_date->format('M d, Y') }}</td>
                    <td>
                        <button class="chip-badge chip-type" style="cursor:pointer; border:none;" onclick="viewRequestDetails({{ $req->id }})">
                            {{ $req->items->count() }} item(s)
                        </button>
                    </td>
                    <td>
                        @if($req->status==='pending')
                            <span class="chip-badge" style="background:#fff8f0;color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @elseif($req->status==='approved')
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Approved</span>
                        @elseif($req->status==='partial')
                            <span class="chip-badge chip-campus"><i class="ti ti-circle-half-2" style="font-size:10px"></i> Partial</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $req->reviewer->name ?? '—' }}</td>
                    <td style="font-size:11.5px; color:var(--text-muted); white-space:nowrap;">
                        @if($req->reviewed_at)
                            {{ $req->reviewed_at->format('M d, Y') }}<br>
                            <span style="font-size:10.5px;">{{ $req->reviewed_at->format('h:i A') }}</span>
                        @else
                            <span style="color:#ccc;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" onclick="viewRequestDetails({{ $req->id }})" title="View Details">
                                <i class="ti ti-eye"></i>
                            </button>
                            @if(in_array($req->status, ['approved','partial']))
                            <a href="{{ route('consumable-requests.report', $req->id) }}" target="_blank" class="table-icon-btn" style="background:#f4f0ff;color:#7c3aed;" title="Report">
                                <i class="ti ti-file-text"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-clipboard-off"></i>
                            <p>No requests yet. <a href="{{ route('consumables') }}" style="color:var(--green-dark); font-weight:600;">Browse items →</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} results</div>
        {{ $requests->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- VIEW DETAILS MODAL --}}
<div class="modal-overlay" id="view-modal">
    <div class="modal-box-lg" style="max-width:620px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-eye"></i> Request Details</div>
            <button class="modal-close" onclick="document.getElementById('view-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <div id="view-modal-content"><div class="empty-state"><i class="ti ti-loader-2"></i><p>Loading...</p></div></div>
    </div>
</div>

<style>
/* Show mobile card list on small screens, hide desktop table */
.mobile-request-list { display: none; }
.desktop-table       { display: block; }
@media(max-width:640px) {
    .mobile-request-list { display: block; }
    .desktop-table       { display: none; }
}
</style>

@endsection

@push('scripts')
<script>
async function viewRequestDetails(id) {
    const modal   = document.getElementById('view-modal');
    const content = document.getElementById('view-modal-content');
    modal.classList.add('open');
    content.innerHTML = '<div class="empty-state"><i class="ti ti-loader-2"></i><p>Loading...</p></div>';

    try {
        const res = await fetch(`/consumable-requests/${id}`);
        const req = await res.json();

        const statusClass = (s) => s==='approved' ? 'chip-status-active' : (s==='rejected' ? 'chip-status-inactive' : '');
        const statusStyle = (s) => s==='pending' ? 'background:#fff8f0;color:#ef9f27;' : (s==='partial' ? 'background:#eff6ff;color:var(--green-dark);' : '');

        content.innerHTML = `
            <div class="detail-section">
                <div class="detail-section-title"><i class="ti ti-info-circle"></i> Request Information</div>
                <div class="detail-grid" style="margin-top:0.75rem;">
                    <div class="detail-row"><span>Reference No.</span><strong>${req.reference_no}</strong></div>
                    <div class="detail-row"><span>Request Date</span><strong>${new Date(req.request_date).toLocaleDateString('en-PH', {year:'numeric',month:'long',day:'numeric'})}</strong></div>
                    <div class="detail-row"><span>Campus</span><strong>${req.campus?.name ?? '—'}</strong></div>
                    <div class="detail-row"><span>Department</span><strong>${req.department}</strong></div>
                    <div class="detail-row"><span>Status</span><strong>${req.status.charAt(0).toUpperCase()+req.status.slice(1)}</strong></div>
                    <div class="detail-row"><span>Reviewed By</span><strong>${req.reviewer?.name ?? 'Pending review'}</strong></div>
                    <div class="detail-row"><span>Approved Date</span><strong>${req.reviewed_at ? new Date(req.reviewed_at).toLocaleString('en-PH', {year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}) : '—'}</strong></div>
                </div>
            </div>
            <br>
            <div class="detail-section">
                <div class="detail-section-title"><i class="ti ti-list"></i> Requested Items</div>
                <div style="overflow-x:auto; margin-top:0.75rem;">
                    <table class="data-table">
                        <thead><tr><th>Item</th><th>Qty</th><th>Purpose</th><th>Release Date</th><th>Status</th><th>Rejection Reason</th></tr></thead>
                        <tbody>
                            ${req.items.map(i => `
                            <tr>
                                <td>${i.consumable?.item_name ?? '—'}</td>
                                <td>${i.quantity} ${i.consumable?.unit ?? ''}</td>
                                <td style="font-size:12px;">${i.purpose ?? '—'}</td>
                                <td style="font-size:11.5px;">${i.release_date ? new Date(i.release_date.substring(0,10)+'T00:00:00').toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'}) : '—'}</td>
                                <td><span class="chip-badge ${statusClass(i.status)}" style="${statusStyle(i.status)}">${i.status}</span></td>
                                <td style="font-size:11.5px; color:#888;">${i.rejection_reason ?? '—'}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="detail-section">
                <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
                <div class="detail-grid" style="margin-top:0.75rem;">
                    <div class="detail-row"><span>Approved By</span><strong>${req.approved_by ?? '—'}</strong></div>
                    <div class="detail-row"><span>Supply Officer</span><strong>${req.supply_officer ?? '—'}</strong></div>
                </div>
            </div>
            ${(req.status==='approved'||req.status==='partial') ? `
            <div style="text-align:center; margin-top:1.25rem;">
                <a href="/consumable-requests/${req.id}/report" target="_blank"
                   class="modal-btn-primary" style="display:inline-flex; width:auto; padding:10px 28px; text-decoration:none; background:var(--green-dark);">
                    <i class="ti ti-file-text"></i> Generate Release Report
                </a>
            </div>` : ''}
        `;
    } catch(e) {
        content.innerHTML = '<div class="empty-state"><i class="ti ti-alert-circle"></i><p>Failed to load request details.</p></div>';
    }
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush