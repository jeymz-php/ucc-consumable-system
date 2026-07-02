@extends('layouts.app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')

@php $user = auth()->user(); @endphp

{{-- Profile Card --}}
<div class="card" style="margin-bottom:1.25rem; max-width:700px;">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-user-circle"></i> My Profile</div>
    </div>
    <div class="card-body">
        <div style="display:flex; align-items:center; gap:1.25rem; margin-bottom:1.25rem; flex-wrap:wrap;">
            <div style="width:64px; height:64px; border-radius:50%;
                        background:var(--green-dark); color:#fff;
                        display:flex; align-items:center; justify-content:center;
                        font-size:26px; font-weight:700; flex-shrink:0; text-transform:uppercase;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <div style="font-size:18px; font-weight:700; color:var(--text-primary);">{{ $user->name }}</div>
                <div style="font-size:13px; color:var(--text-muted); margin-top:2px;">{{ $user->email }}</div>
                <div style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">
                    @if($user->role === 'superadmin')
                        <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">Super Admin</span>
                    @elseif($user->role === 'admin')
                        <span class="chip-badge chip-campus">Admin</span>
                    @else
                        <span class="chip-badge chip-status-active">User</span>
                    @endif
                    <span class="chip-badge" style="background:var(--blue-light); color:var(--blue);">
                        <i class="ti ti-package" style="font-size:10px;"></i> CS Account
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-row">
                <span>Full Name</span>
                <strong>{{ $user->name }}</strong>
            </div>
            <div class="detail-row">
                <span>Email Address</span>
                <strong>{{ $user->email }}</strong>
            </div>
            <div class="detail-row">
                <span>Role</span>
                <strong>{{ ucfirst($user->role) }}</strong>
            </div>
            <div class="detail-row">
                <span>Campus</span>
                <strong>{{ $user->campus->name ?? '—' }}</strong>
            </div>
            <div class="detail-row">
                <span>Department</span>
                <strong>{{ $user->department->department_name ?? '—' }}</strong>
            </div>
            <div class="detail-row">
                <span>Phone</span>
                <strong>{{ $user->phone ?? '—' }}</strong>
            </div>
            <div class="detail-row">
                <span>Account Status</span>
                <strong>
                    @if($user->is_active)
                        <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px;"></i> Active</span>
                    @else
                        <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px;"></i> Inactive</span>
                    @endif
                </strong>
            </div>
            <div class="detail-row">
                <span>Member Since</span>
                <strong>{{ $user->created_at->format('M d, Y') }}</strong>
            </div>
        </div>
    </div>
</div>

{{-- Change Password --}}
<div class="card" style="margin-bottom:1.25rem; max-width:700px;">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-lock-password"></i> Password</div>
    </div>
    <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <div>
            <div style="font-size:13.5px; font-weight:600;">Change Your Password</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:3px;">
                Keep your account secure with a strong password. Update it regularly.
            </div>
        </div>
        <button class="btn-table-action green" style="flex-shrink:0;" onclick="openChangePassword()">
            <i class="ti ti-lock-password"></i> Change Password
        </button>
    </div>
</div>

{{-- Pending Deletion Notice --}}
@if($pendingRequest)
<div class="card" style="margin-bottom:1.25rem; max-width:700px; border-color:#ef9f27;">
    <div class="card-body" style="display:flex; align-items:flex-start; gap:12px; background:#fff8f0; border-radius:12px;">
        <i class="ti ti-clock" style="color:#ef9f27; font-size:22px; margin-top:2px; flex-shrink:0;"></i>
        <div>
            <div style="font-weight:700; color:#b87800; font-size:14px;">Account Deletion Request Pending</div>
            <p style="font-size:13px; color:#7a5500; margin-top:4px; line-height:1.5;">
                You requested account deletion on <strong>{{ $pendingRequest->created_at->format('M d, Y h:i A') }}</strong>.
                An administrator will review this request. Your account remains fully active until then.
            </p>
        </div>
    </div>
</div>
@endif

{{-- Danger Zone --}}
<div class="card" style="max-width:700px; border-color:#fdd;">
    <div class="card-header">
        <div class="card-title" style="color:var(--red);">
            <i class="ti ti-alert-triangle"></i> Danger Zone
        </div>
    </div>
    <div class="card-body" style="display:flex; flex-direction:column; gap:1rem;">

        {{-- Deactivate --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    gap:1rem; padding:1rem; border:1px solid var(--border);
                    border-radius:10px; flex-wrap:wrap;">
            <div>
                <div style="font-weight:600; font-size:13.5px;">Deactivate My Account</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:3px; max-width:440px;">
                    Temporarily disable your account. You can reactivate it anytime by simply logging in again.
                </div>
            </div>
            <button class="btn-table-action" style="background:#fff8f0; color:#ef9f27; flex-shrink:0;"
                    onclick="document.getElementById('deactivate-modal').classList.add('open');">
                <i class="ti ti-user-pause"></i> Deactivate
            </button>
        </div>

        {{-- Request Deletion --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    gap:1rem; padding:1rem; border:1px solid var(--border);
                    border-radius:10px; flex-wrap:wrap;">
            <div>
                <div style="font-weight:600; font-size:13.5px;">Request Account Deletion</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:3px; max-width:440px;">
                    Permanently delete your account and all associated data. This requires administrator approval.
                </div>
            </div>
            @if(!$pendingRequest)
            <button class="btn-table-action" style="background:#fff5f5; color:var(--red); flex-shrink:0;"
                    onclick="document.getElementById('delete-request-modal').classList.add('open');">
                <i class="ti ti-trash"></i> Request Deletion
            </button>
            @else
            <button class="btn-table-action" style="background:#f5f5f5; color:#999; flex-shrink:0; cursor:not-allowed;" disabled>
                <i class="ti ti-clock"></i> Pending Review
            </button>
            @endif
        </div>

    </div>
</div>

{{-- DEACTIVATE MODAL --}}
<div class="modal-overlay" id="deactivate-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-user-pause" style="color:#ef9f27;"></i> Deactivate Account
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('deactivate-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1.25rem; line-height:1.6;">
            Your account will be <strong>temporarily deactivated</strong> and you will be logged out immediately.
            You can reactivate it anytime by logging in again.
        </p>
        <form method="POST" action="{{ route('account.deactivate') }}">
            @csrf
            <div style="display:flex; align-items:flex-start; gap:8px; margin-bottom:1.25rem;">
                <input type="checkbox" name="confirm" id="confirm-deactivate"
                       required style="margin-top:3px; accent-color:#ef9f27; flex-shrink:0; cursor:pointer;">
                <label for="confirm-deactivate" style="font-size:13px; color:#444; cursor:pointer;">
                    I understand and want to deactivate my account.
                </label>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;">
                <i class="ti ti-user-pause"></i> Confirm Deactivation
            </button>
        </form>
    </div>
</div>

{{-- DELETE REQUEST MODAL --}}
<div class="modal-overlay" id="delete-request-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-trash" style="color:var(--red);"></i> Request Account Deletion
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('delete-request-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            This sends a deletion request to an administrator for approval.
            Your account will remain active until approved. To confirm, type exactly:
        </p>
        <div style="background:#fff5f5; border:1.5px solid #e24b4a; border-radius:8px;
                    padding:10px 14px; margin-bottom:1rem;
                    font-size:13px; font-weight:600; color:#c0392b; text-align:center;">
            Delete {{ $user->name }}
        </div>
        <form method="POST" action="{{ route('account.request-deletion') }}">
            @csrf
            <div class="modal-form-group">
                <input type="text" name="confirmation_text" class="modal-input"
                       placeholder="Type the confirmation text above"
                       required autocomplete="off">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">
                    Reason <span style="text-transform:none; font-weight:400; font-size:10px;">(optional)</span>
                </div>
                <textarea name="reason" class="modal-input" rows="3"
                          style="padding-top:10px; resize:none;"
                          placeholder="Why do you want to delete your account?"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--red);">
                <i class="ti ti-send"></i> Submit Deletion Request
            </button>
        </form>
    </div>
</div>

{{-- REACTIVATION MODAL (shows on login if account was deactivated) --}}
@if(session('show_reactivate_modal'))
<div class="modal-overlay open" id="reactivate-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-user-check" style="color:var(--green-dark);"></i> Reactivate Account
            </div>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1.25rem; line-height:1.6;">
            Your account is currently deactivated. Would you like to reactivate it and continue?
        </p>
        <div style="display:flex; gap:10px;">
            <form method="POST" action="{{ route('account.cancel-reactivate') }}" style="flex:1;">
                @csrf
                <button type="submit" class="btn-back-link" style="width:100%; justify-content:center;">
                    Cancel
                </button>
            </form>
            <form method="POST" action="{{ route('account.reactivate') }}" style="flex:1;">
                @csrf
                <button type="submit" class="modal-btn-primary" style="margin:0;">
                    <i class="ti ti-user-check"></i> Reactivate
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => {
        // Don't close reactivation modal on outside click
        if (o.id === 'reactivate-modal') return;
        if (e.target === o) o.classList.remove('open');
    });
});
</script>
@endpush