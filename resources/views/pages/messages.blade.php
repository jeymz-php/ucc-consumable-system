@extends('layouts.app')
@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')

<div class="hero-banner" style="margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-messages"></i> {{ $isAdmin ? 'All Tickets' : 'My Messages' }}</div>
        <p class="hero-sub">{{ $isAdmin ? 'Manage all user support tickets and inquiries.' : 'View your conversations with the admin team.' }}</p>
    </div>
    @if(!$isAdmin)
    <div class="hero-right">
        <button class="btn-add" onclick="document.getElementById('new-ticket-modal').classList.add('open');">
            <i class="ti ti-plus"></i> New Ticket
        </button>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-ticket"></i> Conversations ({{ $conversations->total() }})</div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ticket No.</th>
                    @if($isAdmin)<th>User</th>@endif
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Last Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conv)
                @php
                    $unread = $isAdmin
                        ? $conv->messages()->where('sender_type','user')->where('is_read',false)->count()
                        : $conv->messages()->where('sender_type','admin')->where('is_read',false)->count();
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:6px;">
                            @if($unread > 0)
                            <span style="width:8px; height:8px; border-radius:50%; background:var(--green-dark); flex-shrink:0;"></span>
                            @endif
                            <span style="font-family:monospace; font-size:12px; font-weight:600;">{{ $conv->ticket_no }}</span>
                        </div>
                    </td>
                    @if($isAdmin)
                    <td>
                        <div class="cell-primary">{{ $conv->user->name ?? '—' }}</div>
                        <div class="cell-secondary">{{ $conv->user->email ?? '' }}</div>
                    </td>
                    @endif
                    <td style="font-size:13px;">{{ $conv->subject }}</td>
                    <td>
                        @if($conv->status === 'open')
                            <span class="chip-badge chip-campus">Open</span>
                        @elseif($conv->status === 'resolved')
                            <span class="chip-badge chip-status-active">Resolved</span>
                        @else
                            <span class="chip-badge chip-status-inactive">Closed</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--text-muted); max-width:200px;">
                        {{ Str::limit($conv->lastMessage?->body ?? 'No messages yet', 50) }}
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $conv->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('messages.show', $conv) }}" class="table-icon-btn view" title="Open">
                            <i class="ti ti-message-circle"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isAdmin ? 7 : 6 }}">
                        <div class="empty-state">
                            <i class="ti ti-messages-off"></i>
                            <p>No conversations yet. {{ !$isAdmin ? 'Click "New Ticket" to start one.' : '' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($conversations->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $conversations->firstItem() }} to {{ $conversations->lastItem() }} of {{ $conversations->total() }} results</div>
        {{ $conversations->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- NEW TICKET MODAL --}}
<div class="modal-overlay" id="new-ticket-modal">
    <div class="modal-box-sm" style="max-width:500px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-ticket"></i> Open New Ticket</div>
            <button class="modal-close" onclick="document.getElementById('new-ticket-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('messages.store') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Subject *</div>
                <input type="text" name="subject" class="modal-input" required
                       placeholder="e.g. Inquiry about my request REQ-20260702-001">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Message *</div>
                <textarea name="body" class="modal-input" rows="5" required
                          style="padding-top:10px; resize:vertical;"
                          placeholder="Describe your concern or question..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-send"></i> Submit Ticket
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush