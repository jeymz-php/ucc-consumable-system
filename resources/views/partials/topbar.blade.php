<header class="topbar">

    <div class="topbar-left">
        <button class="topbar-btn" onclick="toggleSidebar()" style="display:none;" id="menu-btn">
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
            <div class="chip"><i class="ti ti-calendar"></i> {{ now()->format('M d, Y') }}</div>
            <div class="chip" id="live-clock">{{ now()->format('h:i:s A') }}</div>
        </div>

        {{-- Notifications Bell --}}
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

            <div id="notif-dropdown" class="settings-dropdown" style="width:340px; right:0;">
                <div class="settings-header" style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div class="settings-user-name">Notifications</div>
                        <div class="settings-user-email" id="notif-summary">No new notifications</div>
                    </div>
                </div>
                <div id="notif-list" style="max-height:360px; overflow-y:auto;"></div>
            </div>
        </div>

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

                <a href="{{ route('account.settings') }}" class="settings-item">
                    <i class="ti ti-settings-2"></i> Account Settings
                </a>

                <a href="#" class="settings-item" onclick="openChangePassword()">
                    <i class="ti ti-lock-password"></i> Change Password
                </a>

                @if(in_array($role, ['admin','superadmin']))
                <a href="{{ route('system.settings') }}" class="settings-item">
                    <i class="ti ti-adjustments-horizontal"></i> System Settings
                </a>
                @endif

                <div class="settings-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="settings-item settings-logout" style="width:100%; text-align:left;">
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
            <div class="modal-title-sm"><i class="ti ti-lock-password"></i> Change Password</div>
            <button class="modal-close" onclick="closeChangePassword()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('password.change') }}" id="change-pass-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Current Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock modal-input-icon"></i>
                    <input type="password" name="current_password" class="modal-input"
                           placeholder="Enter current password" id="cur-pass">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('cur-pass', this)"></i>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">New Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock-open modal-input-icon"></i>
                    <input type="password" name="password" class="modal-input"
                           placeholder="Minimum 8 characters" id="new-pass" oninput="modalStrength()">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('new-pass', this)"></i>
                </div>
                <div class="modal-strength-bar">
                    <div class="modal-seg" id="ms1"></div>
                    <div class="modal-seg" id="ms2"></div>
                    <div class="modal-seg" id="ms3"></div>
                    <div class="modal-seg" id="ms4"></div>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Confirm New Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock-check modal-input-icon"></i>
                    <input type="password" name="password_confirmation" class="modal-input"
                           placeholder="Re-enter new password" id="conf-pass" oninput="modalMatch()">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('conf-pass', this)"></i>
                </div>
                <div class="modal-hint" id="conf-pass-hint"></div>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-check"></i> Update Password
            </button>
        </form>
    </div>
</div>

<script>
// ── SOUND ──
function playNotifSound() {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(660, ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.45);
    } catch(e) {}
}

// ── NOTIFICATIONS ──
let prevNotifCount = 0;

function toggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('notif-dropdown');
    if (dd && !e.target.closest('[onclick*="toggleNotifDropdown"]') && !dd.contains(e.target)) {
        dd.classList.remove('open');
    }
});

function getReadNotifIds() {
    try { return JSON.parse(localStorage.getItem('read_notif_ids_cs') || '[]'); }
    catch (e) { return []; }
}
function markNotifAsRead(type, id) {
    const key     = `${type}-${id}`;
    const readIds = getReadNotifIds();
    if (!readIds.includes(key)) {
        readIds.push(key);
        localStorage.setItem('read_notif_ids_cs', JSON.stringify(readIds));
    }
}

async function pollNotifications() {
    const badge   = document.getElementById('notif-badge');
    const list    = document.getElementById('notif-list');
    const summary = document.getElementById('notif-summary');
    if (!badge) return;

    try {
        const res     = await fetch('{{ route("notifications.poll") }}');
        const data    = await res.json();
        const readIds = getReadNotifIds();
        const isAdmin = {{ in_array(auth()->user()->role, ['admin','superadmin']) ? 'true' : 'false' }};

        const unread = (data.requests || []).filter(r => !readIds.includes(`${r.type}-${r.id}`)).length;

        // ── Sound on new ──
        if (unread > prevNotifCount && prevNotifCount !== 0) {
            playNotifSound();
        }
        prevNotifCount = unread;

        if (unread > 0) {
            badge.style.display = 'flex';
            badge.textContent   = unread;
            const parts = [];
            if (data.request_count > 0) parts.push(`${data.request_count} request update(s)`);
            if (data.message_count > 0) parts.push(`${data.message_count} new message(s)`);
            if (data.deletion_count  > 0) parts.push(`${data.deletion_count} deletion request(s)`);
            if (data.consumable_count > 0) parts.push(`${data.consumable_count} consumable request(s)`);
            summary.textContent = parts.join(', ') || `${unread} new notification(s)`;
        } else {
            badge.style.display = 'none';
            summary.textContent = 'No new notifications';
        }

        list.innerHTML = (data.requests || []).map(r => {
            const isRead   = readIds.includes(`${r.type}-${r.id}`);
            const rowStyle = isRead ? 'opacity:0.55;' : '';
            const dot      = !isRead
                ? '<span style="width:7px;height:7px;border-radius:50%;background:var(--green-dark);display:inline-block;margin-left:4px;"></span>'
                : '';

            // ── REQUEST UPDATE (user-facing) ──
            if (r.type === 'request_update') {
                const sColor = r.status === 'approved' ? 'var(--green-dark)' : (r.status === 'rejected' ? '#e24b4a' : '#ef9f27');
                const sBg    = r.status === 'approved' ? 'var(--green-light)' : (r.status === 'rejected' ? '#fff5f5' : '#fff8f0');
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:${sBg};color:${sColor};padding:2px 7px;border-radius:10px;text-transform:uppercase;">
                            Request ${r.status}
                        </span>${dot}
                    </div>
                    <div style="font-size:13px;font-weight:600;">${r.title}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="{{ route('consumable-requests') }}"
                       onclick="markNotifAsRead('request_update', ${r.id})"
                       style="display:block;text-align:center;padding:6px;border-radius:6px;background:var(--green-light);color:var(--green-dark);font-size:11.5px;font-weight:600;text-decoration:none;">
                        <i class="ti ti-clipboard-list"></i> View My Requests
                    </a>
                </div>`;
            }

            // ── ADMIN MESSAGE (user-facing) ──
            if (r.type === 'message') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:#eff6ff;color:#1a56db;padding:2px 7px;border-radius:10px;text-transform:uppercase;">
                            Admin Message
                        </span>${dot}
                    </div>
                    <div style="font-size:13px;font-weight:600;">${r.title}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="{{ url('/messages') }}/${r.id}"
                       onclick="markNotifAsRead('message', ${r.id})"
                       style="display:block;text-align:center;padding:6px;border-radius:6px;background:#eff6ff;color:#1a56db;font-size:11.5px;font-weight:600;text-decoration:none;">
                        <i class="ti ti-message-circle"></i> Open Conversation
                    </a>
                </div>`;
            }

            // ── ADMIN: DELETION REQUEST ──
            if (r.type === 'deletion') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:#fff5f5;color:#e24b4a;padding:2px 7px;border-radius:10px;text-transform:uppercase;">Account Deletion</span>
                        ${dot}
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
            }

            // ── ADMIN: CONSUMABLE REQUEST ──
            if (r.type === 'consumable') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px;font-weight:700;background:var(--green-light);color:var(--green-dark);padding:2px 7px;border-radius:10px;text-transform:uppercase;">Consumable Request</span>
                        ${dot}
                    </div>
                    <div style="font-size:13px;font-weight:600;">${r.title}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="{{ route('consumable-requests') }}?highlight=${r.id}"
                       onclick="markNotifAsRead('consumable', ${r.id})"
                       style="display:block;text-align:center;padding:6px;border-radius:6px;background:var(--green-light);color:var(--green-dark);font-size:11.5px;font-weight:600;text-decoration:none;">
                        <i class="ti ti-eye"></i> Review Request
                    </a>
                </div>`;
            }

            return '';
        }).join('') || '<div style="padding:20px;text-align:center;font-size:12px;color:#999;">No new notifications.</div>';

    } catch(e) { /* silent fail */ }
}

async function approveDeletion(id) {
    if (!confirm('Permanently delete this user account? This cannot be undone.')) return;
    await fetch(`/notifications/${id}/approve`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    pollNotifications();
}
async function rejectDeletion(id) {
    await fetch(`/notifications/${id}/reject`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    pollNotifications();
}

// Start polling
if (document.getElementById('notif-badge')) {
    pollNotifications();
    setInterval(pollNotifications, 8000);
}

// ── SETTINGS ──
function toggleSettings() {
    document.getElementById('settings-dropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('settings-wrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('settings-dropdown');
        if (dd) dd.classList.remove('open');
    }
});

// ── CHANGE PASSWORD ──
function openChangePassword() {
    document.getElementById('settings-dropdown').classList.remove('open');
    document.getElementById('change-password-modal').classList.add('open');
}
function closeChangePassword() {
    document.getElementById('change-password-modal').classList.remove('open');
}
function toggleModalPass(id, icon) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text';     icon.classList.replace('ti-eye','ti-eye-off'); }
    else                         { inp.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
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
    if (pass === conf) { hint.textContent = 'Passwords match.';        hint.className = 'modal-hint success'; }
    else               { hint.textContent = 'Passwords do not match.'; hint.className = 'modal-hint error'; }
}

// Mobile menu
if (window.innerWidth <= 768) { const btn = document.getElementById('menu-btn'); if (btn) btn.style.display = 'flex'; }
window.addEventListener('resize', () => {
    const btn = document.getElementById('menu-btn');
    if (btn) btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
});
</script>