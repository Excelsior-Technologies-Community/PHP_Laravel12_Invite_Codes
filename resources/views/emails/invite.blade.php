<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You've Been Invited!</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #818cf8;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .header p {
            color: #94a3b8;
            font-size: 16px;
        }
        .code-box {
            background: rgba(99, 102, 241, 0.1);
            border: 2px dashed rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .code-box .code {
            font-size: 32px;
            font-weight: bold;
            color: #818cf8;
            letter-spacing: 4px;
            font-family: monospace;
        }
        .code-box .details {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
        }
        .btn {
            display: inline-block;
            background: #818cf8;
            color: #0f172a;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: scale(1.05);
            background: #6366f1;
        }
        .footer {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .footer a {
            color: #818cf8;
            text-decoration: none;
        }
        .features {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin: 20px 0;
        }
        .feature-item {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .feature-item .icon {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
        }
        @media (max-width: 600px) {
            .card { padding: 24px; }
            .features { grid-template-columns: 1fr; }
            .code-box .code { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
                <h1>You've Been Invited!</h1>
                <p>{{ $inviterName }} has invited you to join the platform</p>
            </div>

            <div class="code-box">
                <div class="code">{{ $invite->code }}</div>
                <div class="details">
                    Uses: {{ $invite->uses }} / {{ $invite->max_uses }}
                    @if($invite->expires_at)
                        • Expires: {{ $invite->expires_at->format('M d, Y h:i A') }}
                    @endif
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('register', ['invite' => $invite->code]) }}" class="btn">
                    🚀 Sign Up Now
                </a>
                <p style="color: #64748b; font-size: 13px; margin-top: 12px;">
                    Your invite code will be auto-filled
                </p>
            </div>

            <div class="features">
                <div class="feature-item">
                    <span class="icon">⚡</span>
                    Fast Registration
                </div>
                <div class="feature-item">
                    <span class="icon">🔒</span>
                    Secure Access
                </div>
                <div class="feature-item">
                    <span class="icon">🎯</span>
                    Exclusive Content
                </div>
            </div>

            <div class="footer">
                <p>
                    This invitation was sent to <strong>{{ $invite->email }}</strong>.
                    If you didn't request this, please ignore this email.
                </p>
                <p style="margin-top: 8px;">
                    <a href="{{ url('/') }}">Visit Website</a> •
                    <a href="mailto:support@example.com">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>