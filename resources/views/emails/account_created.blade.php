<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f4f6f8; padding: 40px 20px; }
        .card { background: #fff; max-width: 480px; margin: 0 auto; border-radius: 12px; padding: 40px; }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo-box { display: inline-flex; background: #1a56db; color: #fff; font-weight: 700; font-size: 18px; padding: 10px 20px; border-radius: 8px; }
        h2 { color: #111; font-size: 22px; margin: 0 0 8px; }
        p  { color: #555; font-size: 14px; line-height: 1.6; }
        .password-box { text-align: center; margin: 28px 0; }
        .password-code { font-size: 28px; font-weight: 700; letter-spacing: 6px; color: #1a56db; background: #eff6ff; padding: 16px 32px; border-radius: 10px; display: inline-block; font-family: monospace; }
        .warning { background: #fff8f0; border-left: 3px solid #ef9f27; padding: 12px 16px; border-radius: 6px; margin-top: 20px; font-size: 13px; color: #7a5500; }
        .pending-note { background: #fff5f5; border-left: 3px solid #e24b4a; padding: 12px 16px; border-radius: 6px; margin-top: 12px; font-size: 13px; color: #c0392b; }
        .footer { text-align: center; margin-top: 32px; font-size: 11px; color: #bbb; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><div class="logo-box">UCC-CS</div></div>
        <h2>Account Created — Pending Approval</h2>
        <p>Hello, <strong>{{ $name }}</strong>!</p>
        <p>Your account has been created in the <strong>UCC Consumable Management System</strong>. Your system-generated password is:</p>
        <div class="password-box">
            <div class="password-code">{{ $generatedPassword }}</div>
        </div>
        <div class="warning">
            ⚠️ Please change your password immediately after your first login for security.
        </div>
        <div class="pending-note">
            🔒 Your account is currently <strong>pending approval</strong> by an administrator. You will receive another email once your account has been approved and you can begin logging in.
        </div>
        <div class="footer">© {{ date('Y') }} University of Caloocan City. All rights reserved.</div>
    </div>
</body>
</html>