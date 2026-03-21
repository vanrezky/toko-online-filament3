<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f5;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #2563eb;
            padding: 32px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 32px;
        }
        .email-body p {
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 16px;
        }
        .email-body .highlight {
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
            padding: 16px;
            margin: 24px 0;
        }
        .email-body .highlight p {
            margin: 0;
        }
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        .expiry-notice {
            color: #dc2626;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Reset Password</h1>
        </div>
        <div class="email-body">
            <p>Halo,</p>
            <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>
            
            <div class="highlight">
                <p>Klik tombol di bawah untuk mereset password Anda:</p>
            </div>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>
            
            <p class="expiry-notice">Link reset password ini akan kedaluwarsa dalam {{ $expires }} menit.</p>
            
            <p>Jika Anda tidak meminta reset password, Anda dapat mengabaikan email ini.</p>
            
            <p style="margin-top: 32px; font-size: 14px; color: #6b7280;">
                Pesan ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>