<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-CS | Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif; min-height: 100vh;
            background: #1a6b3a; display: flex;
            align-items: center; justify-content: center;
            padding: 1rem; position: relative; overflow: hidden;
        }
        .bg-photo { position: fixed; inset: 0; background: url('{{ asset("images/ucc-background.jpg") }}') center/cover no-repeat; opacity: 0.12; z-index: 0; }
        .card-wrap { position: relative; z-index: 1; display: flex; width: 100%; max-width: 900px; min-height: 580px; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,0.25); }

        /* LEFT */
        .left-panel { flex: 0.85; background: rgba(20, 90, 48, 0.92); padding: 3rem 2.2rem; display: flex; flex-direction: column; justify-content: center; color: #fff; }
        .ucc-logo { width: 64px; height: 64px; background: rgba(255,255,255,0.92); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; overflow: hidden; padding: 6px; }
        .ucc-logo img { width: 100%; height: 100%; object-fit: contain; }
        .left-panel h2 { font-size: 22px; font-weight: 700; line-height: 1.3; margin-bottom: 0.5rem; }
        .left-panel p  { font-size: 13px; color: rgba(255,255,255,0.65); margin-bottom: 2rem; }
        .feature-list { list-style: none; }
        .feature-list li { display: flex; align-items: center; gap: 10px; font-size: 13px; color: rgba(255,255,255,0.8); margin-bottom: 0.75rem; }
        .feature-list li i { width: 20px; height: 20px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #93c5fd; flex-shrink: 0; }

        /* RIGHT */
        .right-panel { flex: 1; background: #fff; padding: 2.5rem 2.2rem; display: flex; flex-direction: column; overflow-y: auto; }

        /* STEPPER */
        .stepper { display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; gap: 0; }
        .step-item { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .step-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; border: 2px solid #ddd; color: #bbb; background: #fff; transition: all 0.3s; }
        .step-circle.active { border-color: #1a56db; color: #1a56db; }
        .step-circle.done   { border-color: #1a56db; background: #1a56db; color: #fff; }
        .step-label { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; color: #bbb; }
        .step-label.active { color: #1a56db; }
        .step-label.done   { color: #1a56db; }
        .step-line { flex: 1; height: 2px; background: #ddd; margin: 0 8px; margin-bottom: 20px; transition: background 0.3s; max-width: 60px; }
        .step-line.done { background: #1a56db; }

        /* FORM */
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .step-title { font-size: 22px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .step-desc  { font-size: 13px; color: #888; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #555; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .form-label i { font-size: 13px; color: #1a56db; }
        .badge-optional { font-size: 9px; background: #f0f0f0; color: #999; padding: 2px 6px; border-radius: 10px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 15px; pointer-events: none; }
        .input-right { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 15px; cursor: pointer; }
        .form-control { width: 100%; padding: 11px 14px 11px 36px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: 'Inter', sans-serif; color: #111; background: #fff; transition: border-color 0.2s; outline: none; }
        .form-control:focus { border-color: #1a56db; }
        .form-control.error { border-color: #e24b4a; }
        .hint { font-size: 11px; margin-top: 4px; }
        .hint.error   { color: #e24b4a; }
        .hint.success { color: #1a56db; }

        /* OTP Row */
        .otp-row { display: flex; gap: 8px; }
        .otp-row .form-control { flex: 1; }
        .btn-get-code { padding: 11px 16px; background: #1a56db; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: 'Inter', sans-serif; transition: background 0.2s; flex-shrink: 0; }
        .btn-get-code:hover    { background: #1648c0; }
        .btn-get-code:disabled { background: #93c5fd; cursor: not-allowed; }

        /* OTP Digits */
        .otp-inputs { display: flex; gap: 8px; margin-top: 1rem; }
        .otp-digit { width: 44px; height: 52px; text-align: center; font-size: 22px; font-weight: 700; border: 2px solid #e0e0e0; border-radius: 8px; outline: none; color: #111; font-family: 'Inter', sans-serif; transition: border-color 0.2s; }
        .otp-digit:focus { border-color: #1a56db; }
        .otp-digit.filled { border-color: #1a56db; }

        /* Strength */
        .strength-bar { display: flex; gap: 4px; margin-top: 6px; }
        .strength-seg { flex: 1; height: 3px; border-radius: 2px; background: #e0e0e0; transition: background 0.3s; }
        .strength-label { font-size: 11px; color: #aaa; margin-top: 4px; }

        .password-card { border: 1.5px solid #e6f0ff; background: #f8fbff; border-radius: 12px; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; }
        .password-row { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; }
        .password-value { font-size: 16px; font-weight: 700; color: #111; letter-spacing: 0.08em; word-break: break-all; }
        .password-copy { display: inline-flex; align-items: center; justify-content: center; gap: 6px; border: none; border-radius: 8px; padding: 8px 10px; background: #1a56db; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer; }
        .password-copy:hover { background: #1648c0; }
        .password-note { font-size: 12px; color: #5b6675; line-height: 1.6; }

        /* Buttons */
        .btn-primary { width: 100%; padding: 13px; background: #1a56db; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.2s, transform 0.15s; margin-top: 1rem; }
        .btn-primary:hover    { background: #1648c0; transform: translateY(-1px); }
        .btn-primary:disabled { background: #93c5fd; cursor: not-allowed; transform: none; }
        .btn-back { width: 100%; padding: 12px; background: transparent; color: #888; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; margin-top: 0.5rem; transition: border-color 0.2s; }
        .btn-back:hover { border-color: #1a56db; color: #1a56db; }

        .signin-link { text-align: center; font-size: 13px; color: #888; margin-top: 1.2rem; }
        .signin-link a { color: #1a56db; font-weight: 600; text-decoration: none; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: #fff; border-radius: 16px; padding: 2rem; width: 100%; max-width: 480px; max-height: 85vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.2); }
        .modal-title { font-size: 18px; font-weight: 700; color: #111; margin-bottom: 4px; }
        .modal-desc  { font-size: 13px; color: #888; margin-bottom: 1.5rem; }
        .modal-section { border: 1.5px solid #e0e0e0; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; }
        .modal-section h4 { font-size: 13px; font-weight: 600; color: #111; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px; }
        .modal-section h4 i { color: #1a56db; font-size: 16px; }
        .modal-section p { font-size: 12px; color: #666; line-height: 1.7; max-height: 100px; overflow-y: auto; padding-right: 4px; }
        .modal-check { display: flex; align-items: flex-start; gap: 10px; margin-top: 0.75rem; }
        .modal-check input[type="checkbox"] { width: 16px; height: 16px; flex-shrink: 0; accent-color: #1a56db; margin-top: 2px; cursor: pointer; }
        .modal-check label { font-size: 13px; color: #444; cursor: pointer; }
        .btn-create { width: 100%; padding: 13px; background: #93c5fd; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: not-allowed; font-family: 'Inter', sans-serif; margin-top: 1.2rem; transition: background 0.2s; }
        .btn-create.enabled { background: #1a56db; cursor: pointer; }
        .btn-create.enabled:hover { background: #1648c0; }

        .timer { font-size: 12px; color: #888; margin-top: 6px; }
        .timer span { color: #1a56db; font-weight: 600; }

        @media(max-width:640px) {
            body { padding: 0.75rem; }
            .card-wrap { flex-direction: column; min-height: auto; }
            .left-panel { padding: 2rem 1.5rem; min-height: 200px; }
            .right-panel { padding: 1.5rem; }
            .stepper { gap: 0.25rem; }
            .step-label { font-size: 8px; }
            .step-circle { width: 28px; height: 28px; font-size: 12px; }
            .otp-row { flex-direction: column; }
            .btn-get-code { width: 100%; }
            .password-row { flex-direction: column; align-items: flex-start; }
            .password-copy { width: 100%; }
            .modal-box { padding: 1.25rem; margin: 0.75rem; }
        }
    </style>
</head>
<body>
<div class="bg-photo"></div>

<div class="card-wrap">

    {{-- LEFT --}}
    <div class="left-panel">
        <div class="ucc-logo">
            <img src="{{ asset('images/ucc.png') }}" alt="UCC Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'font-size:20px;font-weight:700;color:#1a56db;\'>UCC</span>'">
        </div>
        <h2>Join UCC<br>Consumable System</h2>
        <p>Create your account in minutes</p>
        <ul class="feature-list">
            <li><i class="ti ti-check"></i> Browse consumable inventory</li>
            <li><i class="ti ti-check"></i> Submit stock requests</li>
            <li><i class="ti ti-check"></i> Track request status</li>
            <li><i class="ti ti-check"></i> View consumption history</li>
            <li><i class="ti ti-check"></i> Generate release reports</li>
        </ul>
    </div>

    {{-- RIGHT --}}
    <div class="right-panel">

        {{-- Stepper --}}
        <div class="stepper">
            <div class="step-item">
                <div class="step-circle active" id="circle-1">1</div>
                <div class="step-label active" id="label-1">Email</div>
            </div>
            <div class="step-line" id="line-1"></div>
            <div class="step-item">
                <div class="step-circle" id="circle-2">2</div>
                <div class="step-label" id="label-2">Details</div>
            </div>
        </div>

        {{-- STEP 1: EMAIL --}}
        <div class="step-panel active" id="step-1">
            <div class="step-title">Verify your email</div>
            <div class="step-desc">Enter your email address to receive a verification code.</div>
            <div class="form-group">
                <div class="form-label"><i class="ti ti-mail"></i> Email Address</div>
                <div class="otp-row">
                    <div class="input-wrap" style="flex:1">
                        <i class="ti ti-mail input-icon"></i>
                        <input type="email" class="form-control" id="reg-email" placeholder="Enter your email">
                    </div>
                    <button class="btn-get-code" id="btn-get-code" onclick="sendOtp()">Get Code</button>
                </div>
                <div class="hint" id="email-hint"></div>
                <div class="timer" id="otp-timer" style="display:none">Resend code in <span id="timer-count">60</span>s</div>
            </div>
            <div class="form-group" id="otp-section" style="display:none">
                <div class="form-label"><i class="ti ti-shield-check"></i> Verification Code</div>
                <div class="otp-inputs" id="otp-inputs">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                    <input class="otp-digit" maxlength="1" type="text" inputmode="numeric">
                </div>
                <div class="hint" id="otp-hint"></div>
            </div>
            <button class="btn-primary" id="btn-verify" onclick="verifyOtp()" style="display:none">Verify &amp; Continue</button>
        </div>

        {{-- STEP 2: DETAILS --}}
        <div class="step-panel" id="step-2">
            <div class="step-title">Personal Information</div>
            <div class="step-desc">Tell us a bit about yourself.</div>
            <div class="form-group">
                <div class="form-label"><i class="ti ti-user"></i> Full Name</div>
                <div class="input-wrap">
                    <i class="ti ti-user input-icon"></i>
                    <input type="text" class="form-control" id="reg-name" placeholder="e.g., Juan Dela Cruz">
                </div>
                <div class="hint error" id="name-hint"></div>
            </div>
            <div class="form-group">
                <div class="form-label"><i class="ti ti-building"></i> Campus</div>
                <div class="input-wrap">
                    <i class="ti ti-building input-icon"></i>
                    <select class="form-control" id="reg-campus">
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hint error" id="campus-hint"></div>
            </div>
            <div class="form-group">
                <div class="form-label"><i class="ti ti-briefcase"></i> Department</div>
                <div class="input-wrap">
                    <i class="ti ti-briefcase input-icon"></i>
                    <select class="form-control" id="reg-department" disabled>
                        <option value="">-- Select Campus First --</option>
                    </select>
                </div>
                <div class="hint error" id="dept-hint"></div>
            </div>
            <div class="form-group">
                <div class="form-label">
                    <i class="ti ti-phone"></i> Phone Number
                    <span class="badge-optional">Optional</span>
                </div>
                <div class="input-wrap">
                    <i class="ti ti-phone input-icon"></i>
                    <input type="text" class="form-control" id="reg-phone" placeholder="e.g., 09123456789">
                </div>
                <div class="hint">Phone number is optional but recommended for account recovery.</div>
            </div>
            <button class="btn-primary" onclick="openModal()">Proceed</button>
            <button class="btn-back" onclick="goToStep(1)">← Back</button>
        </div>

        {{-- STEP 3: PASSWORD --}}
        <div class="step-panel" id="step-3">
            <div class="step-title">Your system-generated password</div>
            <div class="step-desc">We created a secure temporary password for your account using your last name and random numbers.</div>
            <div class="form-group">
                <div class="form-label"><i class="ti ti-lock"></i> Temporary Password</div>
                <div class="password-card">
                    <div class="password-row">
                        <span class="password-value" id="generated-password">-</span>
                        <button class="password-copy" type="button" onclick="copyGeneratedPassword()">
                            <i class="ti ti-copy"></i> Copy
                        </button>
                    </div>
                    <div class="password-note">Use this password on your first login. You can change it later from your profile settings.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="hint success" id="password-hint">A password has been generated for you automatically.</div>
            </div>
            <button class="btn-primary" onclick="openModal()">Proceed</button>
            <button class="btn-back" onclick="goToStep(2)">← Back</button>
        </div>

        <div class="signin-link">Already have an account? <a href="{{ route('login') }}">Sign In →</a></div>

    </div>
</div>

{{-- TERMS MODAL --}}
<div class="modal-overlay" id="terms-modal">
    <div class="modal-box">
        <div class="modal-title">Before you proceed</div>
        <div class="modal-desc">Please read and agree to both documents to create your account.</div>
        <div class="modal-section">
            <h4><i class="ti ti-file-text"></i> Terms &amp; Conditions</h4>
            <p>By creating an account on the UCC Consumable Management System, you agree to use the system solely for official university purposes. Unauthorized access, misuse of data, or manipulation of inventory records is strictly prohibited and may result in disciplinary action.</p>
            <div class="modal-check">
                <input type="checkbox" id="chk-terms" onchange="checkModalReady()">
                <label for="chk-terms">I have read and agree to the <strong>Terms &amp; Conditions</strong>.</label>
            </div>
        </div>
        <div class="modal-section">
            <h4><i class="ti ti-shield-lock"></i> Data Privacy Act of 2012 (RA 10173)</h4>
            <p>In compliance with the Data Privacy Act of 2012 (Republic Act No. 10173), the University of Caloocan City collects and processes your personal information solely for the purpose of managing university consumables and related services. Your data will not be shared with third parties without your consent.</p>
            <div class="modal-check">
                <input type="checkbox" id="chk-privacy" onchange="checkModalReady()">
                <label for="chk-privacy">I have read and agree to the <strong>Data Privacy Act of 2012</strong> consent.</label>
            </div>
        </div>
        <button class="btn-create" id="btn-create" onclick="submitForm()" disabled>
            <i class="ti ti-user-plus"></i> Create Account
        </button>
        <button class="btn-back" style="margin-top:0.5rem" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let verifiedEmail = '';
let timerInterval = null;
let generatedPassword = '';

async function sendOtp() {
    const email = document.getElementById('reg-email').value.trim();
    const hint  = document.getElementById('email-hint');
    const btn   = document.getElementById('btn-get-code');
    if (!email) { setHint(hint, 'Please enter your email address.', 'error'); return; }
    btn.disabled = true; btn.textContent = 'Sending...';
    try {
        const res  = await fetch('{{ route("register.send-otp") }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify({email}) });
        const data = await res.json();
        if (!res.ok) { setHint(hint, data.errors?.email?.[0] || data.message, 'error'); btn.disabled = false; btn.textContent = 'Get Code'; return; }
        setHint(hint, 'Code sent! Check your email.', 'success');
        document.getElementById('otp-section').style.display = 'block';
        document.getElementById('btn-verify').style.display  = 'block';
        startTimer(btn); focusOtp();
    } catch (e) { setHint(hint, 'Something went wrong. Try again.', 'error'); btn.disabled = false; btn.textContent = 'Get Code'; }
}

function startTimer(btn) {
    let secs = 60;
    const timerEl = document.getElementById('otp-timer');
    const countEl = document.getElementById('timer-count');
    timerEl.style.display = 'block'; countEl.textContent = secs;
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        secs--; countEl.textContent = secs;
        if (secs <= 0) { clearInterval(timerInterval); timerEl.style.display = 'none'; btn.disabled = false; btn.textContent = 'Resend Code'; }
    }, 1000);
}

function focusOtp() {
    const digits = document.querySelectorAll('.otp-digit');
    digits.forEach((d, i) => {
        d.value = ''; d.classList.remove('filled');
        d.addEventListener('input', () => { d.value = d.value.replace(/\D/g, ''); if (d.value) { d.classList.add('filled'); if (digits[i+1]) digits[i+1].focus(); } else d.classList.remove('filled'); });
        d.addEventListener('keydown', e => { if (e.key === 'Backspace' && !d.value && digits[i-1]) digits[i-1].focus(); });
    }); digits[0].focus();
}

async function verifyOtp() {
    const email  = document.getElementById('reg-email').value.trim();
    const digits = document.querySelectorAll('.otp-digit');
    const code   = Array.from(digits).map(d => d.value).join('');
    const hint   = document.getElementById('otp-hint');
    if (code.length < 6) { setHint(hint, 'Please enter the full 6-digit code.', 'error'); return; }
    try {
        const res  = await fetch('{{ route("register.verify-otp") }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify({email, code}) });
        const data = await res.json();
        if (!res.ok) { setHint(hint, data.message, 'error'); return; }
        verifiedEmail = email; clearInterval(timerInterval); goToStep(2);
    } catch (e) { setHint(hint, 'Something went wrong. Try again.', 'error'); }
}

function goToStep(n) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`step-${n}`).classList.add('active');
    updateStepper(n);
}

function generateSystemPassword(name) {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    const lastName = parts.length > 1 ? parts[parts.length - 1] : (parts[0] || 'user');
    const letters = (lastName.replace(/[^A-Za-z]/g, '') || 'user').toLowerCase().slice(0, 4);
    const prefix = letters.padEnd(4, 'x').slice(0, 4);
    const suffix = String(Math.floor(1000 + Math.random() * 9000));
    return prefix + suffix;
}

function generateAndShowPassword(name) {
    generatedPassword = generateSystemPassword(name);
    document.getElementById('generated-password').textContent = generatedPassword;
}

async function copyGeneratedPassword() {
    const value = generatedPassword || document.getElementById('generated-password').textContent;
    try {
        await navigator.clipboard.writeText(value);
        const hint = document.getElementById('password-hint');
        setHint(hint, 'Password copied to clipboard.', 'success');
    } catch (e) {
        const hint = document.getElementById('password-hint');
        setHint(hint, 'Copy failed. Please select the password manually.', 'error');
    }
}

function updateStepper(current) {
    for (let i = 1; i <= 2; i++) {
        const circle = document.getElementById(`circle-${i}`);
        const label  = document.getElementById(`label-${i}`);
        circle.classList.remove('active','done'); label.classList.remove('active','done');
        if (i < current)       { circle.classList.add('done');   label.classList.add('done');   circle.innerHTML = '<i class="ti ti-check" style="font-size:13px"></i>'; }
        else if (i === current) { circle.classList.add('active'); label.classList.add('active'); circle.textContent = i; }
        else                    { circle.textContent = i; }
    }
    document.getElementById('line-1').classList.toggle('done', current > 1);
}

async function loadDepartments() {
    const deptSel = document.getElementById('reg-department');
    deptSel.innerHTML = '<option value="">Loading...</option>'; deptSel.disabled = true;
    const res  = await fetch(`{{ route('register.departments') }}`);
    const data = await res.json();
    deptSel.innerHTML = '<option value="">-- Select Department --</option>';
    data.forEach(d => { deptSel.innerHTML += `<option value="${d.id}">${d.department_name}</option>`; });
    deptSel.disabled = false;
}
document.addEventListener('DOMContentLoaded', loadDepartments);

function togglePass(id, icon) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('ti-eye','ti-eye-off'); }
    else { inp.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
}

function checkStrength() {
    const val  = document.getElementById('reg-password').value;
    const segs = [document.getElementById('s1'),document.getElementById('s2'),document.getElementById('s3'),document.getElementById('s4')];
    const lbl  = document.getElementById('strength-label');
    let score  = 0;
    if (val.length >= 8) score++; if (/[A-Z]/.test(val)) score++; if (/[0-9]/.test(val)) score++; if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e24b4a','#ef9f27','#1D9E75','#1a56db'];
    const labels = ['Weak','Fair','Good','Strong'];
    segs.forEach((s,i) => s.style.background = i < score ? colors[score-1] : '#e0e0e0');
    lbl.textContent = val.length ? labels[score-1] || '' : ''; lbl.style.color = score ? colors[score-1] : '#aaa';
}

function checkMatch() {
    const pass = document.getElementById('reg-password').value;
    const conf = document.getElementById('reg-confirm').value;
    const hint = document.getElementById('confirm-hint');
    if (!conf) { hint.textContent = ''; return; }
    if (pass === conf) setHint(hint, 'Passwords match.', 'success');
    else setHint(hint, 'Passwords do not match.', 'error');
}

function openModal() {
    const name   = document.getElementById('reg-name').value.trim();
    const campus = document.getElementById('reg-campus').value;
    const dept   = document.getElementById('reg-department').value;
    let valid = true;
    if (!name)   { setHint(document.getElementById('name-hint'),   'Full name is required.', 'error'); valid = false; } else setHint(document.getElementById('name-hint'), '', '');
    if (!campus) { setHint(document.getElementById('campus-hint'), 'Please select a campus.', 'error'); valid = false; } else setHint(document.getElementById('campus-hint'), '', '');
    if (!dept)   { setHint(document.getElementById('dept-hint'),   'Please select a department.', 'error'); valid = false; } else setHint(document.getElementById('dept-hint'), '', '');
    if (!valid) return;

    document.getElementById('chk-terms').checked   = false;
    document.getElementById('chk-privacy').checked = false;
    document.getElementById('btn-create').disabled  = true;
    document.getElementById('btn-create').classList.remove('enabled');
    document.getElementById('terms-modal').classList.add('open');
}
function closeModal() { document.getElementById('terms-modal').classList.remove('open'); }

function checkModalReady() {
    const t = document.getElementById('chk-terms').checked;
    const p = document.getElementById('chk-privacy').checked;
    const btn = document.getElementById('btn-create');
    if (t && p) { btn.disabled = false; btn.classList.add('enabled'); }
    else        { btn.disabled = true;  btn.classList.remove('enabled'); }
}

async function submitForm() {
    const btn = document.getElementById('btn-create');
    btn.textContent = 'Creating account...'; btn.disabled = true;
    const payload = {
        name:          document.getElementById('reg-name').value.trim(),
        email:         verifiedEmail,
        campus_id:     document.getElementById('reg-campus').value,
        department_id: document.getElementById('reg-department').value,
        phone:         document.getElementById('reg-phone').value.trim(),
    };
    try {
        const res  = await fetch('{{ route("register.submit") }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (!res.ok) { btn.textContent = 'Create Account'; btn.disabled = false; btn.classList.add('enabled'); alert(data.message || 'Registration failed.'); return; }
        window.location.href = data.redirect;
    } catch (e) { btn.textContent = 'Create Account'; btn.disabled = false; btn.classList.add('enabled'); alert('Something went wrong. Please try again.'); }
}

function setHint(el, msg, type) { el.textContent = msg; el.className = 'hint' + (type ? ' ' + type : ''); }
</script>
</body>
</html>