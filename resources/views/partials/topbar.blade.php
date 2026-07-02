<header class="topbar">

    <div class="topbar-left">
        {{-- Mobile menu toggle --}}
        <button class="topbar-btn" id="menu-btn" onclick="toggleSidebar()" title="Menu"
                style="display:none;">
            <i class="ti ti-menu-2"></i>
        </button>

        <div class="page-title-bar">
            <i class="ti ti-package" style="color:var(--green-dark); font-size:18px;"></i>
            @yield('page-title', 'Dashboard')

            @php $role = auth()->user()->role; @endphp
            @if($role === 'superadmin')
                <span class="page-badge badge-superadmin">Super Admin</span>
            @elseif($role === 'admin')
                <span class="page-badge badge-admin">Admin</span>
            @else
                <span class="page-badge badge-user">User</span>
            @endif
        </div>
    </div>

    <div class="topbar-right">

        {{-- Date & Time --}}
        <div class="datetime-chip">
            <div class="chip">
                <i class="ti ti-calendar"></i>
                {{ now()->format('M d, Y') }}
            </div>
            <div class="chip" id="live-clock">{{ now()->format('h:i:s A') }}</div>
        </div>

        {{-- Notifications Bell (Admin/Super Admin only) --}}
        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
        <div style="position:relative;">
            <a href="#" class="topbar-btn" title="Notifications"
               onclick="event.preventDefault(); toggleNotifDropdown();">
                <i class="ti ti-bell"></i>
                <span id="notif-badge"
                      style="display:none; position:absolute; top:-4px; right:-4px;
                             background:#e24b4a; color:#fff; font-size:10px; font-weight:700;
                             border-radius:50%; width:18px; height:18px;
                             align-items:center; justify-content:center;">0</span>
            </a>

            <div id="notif-dropdown" class="settings-dropdown" style="width:360px; right:0;">
                <div class="settings-header" style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div class="settings-user-name">Notifications</div>
                        <div class="settings-user-email" id="notif-summary">No pending notifications</div>
                    </div>
                    <a href="{{ route('notifications.index') }}"
                       style="font-size:11px; color:var(--green-dark); font-weight:600; text-decoration:none; white-space:nowrap;">
                        View All →
                    </a>
                </div>
                <div id="notif-list" style="max-height:320px; overflow-y:auto;"></div>
            </div>
        </div>
        @endif

        {{-- Settings Dropdown --}}
        <div class="settings-wrap" id="settings-wrap">
            <button class="topbar-btn" onclick="toggleSettings()" title="Settings">
                <i class="ti ti-settings"></i>
            </button>

            <div class="settings-dropdown" id="settings-dropdown">
                <div class="settings-header">
                    <div class="settings-user-name">{{ auth()->user()->name }}</div>
                    <div class="settings-user-email">{{ auth()->user()->email }}</div>
                </div>

                <a href="#" class="settings-item" onclick="openChangePassword(); return false;">
                    <i class="ti ti-lock-password"></i> Change Password
                </a>
                <a href="{{ route('account.settings') }}" class="settings-item">
                    <i class="ti ti-settings-2"></i> Account Settings
                </a>

                @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                <div class="settings-divider"></div>
                <a href="{{ route('system.settings') }}" class="settings-item">
                    <i class="ti ti-adjustments-horizontal"></i> System Settings
                </a>
                @endif

                <div class="settings-divider"></div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="settings-item settings-logout" style="width:100%;">
                        <i class="ti ti-logout"></i> Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>

{{-- Change Password Modal --}}
<div class="modal-overlay" id="change-password-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-lock-password"></i> Change Password
            </div>
            <button class="modal-close" onclick="closeChangePassword()">
                <i class="ti ti-x"></i>
            </button>
        </div>

        @if(session('password_error'))
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <i class="ti ti-alert-circle"></i>
            <div class="alert-text">{{ session('password_error') }}</div>
        </div>
        @endif

        @if(session('password_success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <i class="ti ti-circle-check"></i>
            <div class="alert-text">{{ session('password_success') }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.change') }}" id="change-pass-form">
            @csrf @method('PUT')

            <div class="modal-form-group">
                <div class="modal-label">Current Password</div>
                <div style="position:relative;">
                    <i class="ti ti-lock" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:15px;pointer-events:none;"></i>
                    <input type="password" name="current_password" class="modal-input"
                           placeholder="Enter current password"
                           style="padding-left:36px; padding-right:36px;"
                           id="cur-pass">
                    <i class="ti ti-eye" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#aaa;cursor:pointer;font-size:15px;"
                       onclick="toggleModalPass('cur-pass', this)"></i>
                </div>
            </div>

            <div class="modal-form-group">
                <div class="modal-label">New Password</div>
                <div style="position:relative;">
                    <i class="ti ti-lock-open" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:15px;pointer-events:none;"></i>
                    <input type="password" name="password" class="modal-input"
                           placeholder="Minimum 8 characters"
                           style="padding-left:36px; padding-right:36px;"
                           id="new-pass" oninput="modalStrength()">
                    <i class="ti ti-eye" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#aaa;cursor:pointer;font-size:15px;"
                       onclick="toggleModalPass('new-pass', this)"></i>
                </div>
                <div style="display:flex; gap:4px; margin-top:5px;">
                    <div style="flex:1;height:3px;border-radius:2px;background:#e0e0e0;" id="ms1"></div>
                    <div style="flex:1;height:3px;border-radius:2px;background:#e0e0e0;" id="ms2"></div>
                    <div style="flex:1;height:3px;border-radius:2px;background:#e0e0e0;" id="ms3"></div>
                    <div style="flex:1;height:3px;border-radius:2px;background:#e0e0e0;" id="ms4"></div>
                </div>
            </div>

            <div class="modal-form-group">
                <div class="modal-label">Confirm New Password</div>
                <div style="position:relative;">
                    <i class="ti ti-lock-check" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:15px;pointer-events:none;"></i>
                    <input type="password" name="password_confirmation" class="modal-input"
                           placeholder="Re-enter new password"
                           style="padding-left:36px; padding-right:36px;"
                           id="conf-pass" oninput="modalMatch()">
                    <i class="ti ti-eye" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#aaa;cursor:pointer;font-size:15px;"
                       onclick="toggleModalPass('conf-pass', this)"></i>
                </div>
                <div class="modal-hint" id="conf-pass-hint"></div>
            </div>

            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-check"></i> Update Password
            </button>
        </form>
    </div>
</div>

<style>
/* Show menu button on mobile */
@media(max-width:768px) {
    #menu-btn { display: flex !important; }
    .datetime-chip .chip:first-child { display: none; }
}
@media(max-width:480px) {
    #notif-dropdown { width: calc(100vw - 2rem) !important; right: -60px !important; }
    .settings-dropdown { min-width: 200px !important; }
}
</style>

<script>
function openChangePassword() {
    document.getElementById('settings-dropdown').classList.remove('open');
    document.getElementById('change-password-modal').classList.add('open');
}
function closeChangePassword() {
    document.getElementById('change-password-modal').classList.remove('open');
}

function toggleModalPass(id, icon) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        inp.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
    }
}

function modalStrength() {
    const val  = document.getElementById('new-pass').value;
    const segs = ['ms1','ms2','ms3','ms4'].map(id => document.getElementById(id));
    let score  = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e24b4a','#ef9f27','#1D9E75','#1a6b3a'];
    segs.forEach((s,i) => s.style.background = i < score ? colors[score-1] : '#e0e0e0');
}

function modalMatch() {
    const pass = document.getElementById('new-pass').value;
    const conf = document.getElementById('conf-pass').value;
    const hint = document.getElementById('conf-pass-hint');
    if (!conf) { hint.textContent = ''; return; }
    if (pass === conf) { hint.textContent = 'Passwords match.'; hint.className = 'modal-hint success'; }
    else               { hint.textContent = 'Passwords do not match.'; hint.className = 'modal-hint error'; }
}

function toggleNotifDropdown() {
    document.getElementById('notif-dropdown').classList.toggle('open');
}

// Close change password modal if opened by session flag
@if(session('show_password_modal'))
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('change-password-modal').classList.add('open');
});
@endif

@if(in_array(auth()->user()->role, ['admin', 'superadmin']))
// ── NOTIFICATION POLLING ──
function getReadNotifIds() {
    try { return JSON.parse(localStorage.getItem('cs_read_notif_ids') || '[]'); } catch(e) { return []; }
}
function markNotifAsRead(type, id) {
    const key = `${type}-${id}`;
    const ids = getReadNotifIds();
    if (!ids.includes(key)) { ids.push(key); localStorage.setItem('cs_read_notif_ids', JSON.stringify(ids)); }
}

async function pollNotifications() {
    const badge   = document.getElementById('notif-badge');
    const list    = document.getElementById('notif-list');
    const summary = document.getElementById('notif-summary');
    if (!badge) return;
    try {
        const res  = await fetch('{{ route("notifications.poll") }}');
        const data = await res.json();
        const readIds = getReadNotifIds();

        const unread = data.requests.filter(r => !readIds.includes(`${r.type}-${r.id}`)).length;

        if (unread > 0) {
            badge.style.display = 'flex';
            badge.textContent   = unread;
            const parts = [];
            if (data.deletion_count  > 0) parts.push(`${data.deletion_count} account deletion`);
            if (data.consumable_count > 0) parts.push(`${data.consumable_count} consumable request`);
            summary.textContent = parts.join(', ');
        } else {
            badge.style.display = 'none';
            summary.textContent = data.count > 0 ? 'All caught up' : 'No pending notifications';
        }

        list.innerHTML = data.requests.map(r => {
            const isRead   = readIds.includes(`${r.type}-${r.id}`);
            const rowStyle = isRead ? 'opacity:0.55;' : '';
            const unreadDot = !isRead
                ? '<span style="width:7px;height:7px;border-radius:50%;background:var(--green-dark);display:inline-block;"></span>'
                : '';

            if (r.type === 'deletion') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:#fff5f5;color:#e24b4a;padding:2px 7px;border-radius:10px;text-transform:uppercase;">Account Deletion</span>
                        ${unreadDot}
                    </div>
                    <div style="font-size:13px;font-weight:600;">${r.title}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin:2px 0 6px;">${r.subtitle} • ${r.created_at}</div>
                    ${r.reason ? `<div style="font-size:12px;color:#666;margin-bottom:8px;font-style:italic;">"${r.reason}"</div>` : ''}
                    <div style="display:flex;gap:6px;">
                        <button onclick="approveDeletion(${r.id})" style="flex:1;padding:6px;border:none;border-radius:6px;background:#fff5f5;color:#e24b4a;font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;">
                            <i class="ti ti-check"></i> Approve & Delete
                        </button>
                        <button onclick="rejectDeletion(${r.id})" style="flex:1;padding:6px;border:none;border-radius:6px;background:var(--green-light);color:var(--green-dark);font-size:11.5px;font-weight:600;cursor:pointer;font-family:inherit;">
                            <i class="ti ti-x"></i> Reject
                        </button>
                    </div>
                </div>`;
            } else {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:var(--green-light);color:var(--green-dark);padding:2px 7px;border-radius:10px;text-transform:uppercase;">Consumable Request</span>
                        ${unreadDot}
                    </div>
                    <div style="font-size:13px;font-weight:600;">${r.title}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="#" onclick="reviewRequest(event, ${r.id})"
                       style="display:block;text-align:center;padding:6px;border-radius:6px;background:var(--green-light);color:var(--green-dark);font-size:11.5px;font-weight:600;text-decoration:none;">
                        <i class="ti ti-eye"></i> Review Request
                    </a>
                </div>`;
            }
        }).join('') || '<div style="padding:20px;text-align:center;font-size:12px;color:#999;">No pending notifications.</div>';

    } catch(e) { /* silent fail */ }
}

async function approveDeletion(id) {
    if (!confirm('Permanently delete this account? This cannot be undone.')) return;
    await fetch(`/notifications/${id}/approve`, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} });
    markNotifAsRead('deletion', id);
    pollNotifications();
}
async function rejectDeletion(id) {
    if (!confirm('Reject this deletion request?')) return;
    await fetch(`/notifications/${id}/reject`, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} });
    markNotifAsRead('deletion', id);
    pollNotifications();
}
function reviewRequest(e, id) {
    e.preventDefault();
    markNotifAsRead('consumable', id);
    pollNotifications();
    window.location.href = `{{ route('consumable-requests') }}?highlight=${id}`;
}

pollNotifications();
setInterval(pollNotifications, 8000);
@endif
</script>