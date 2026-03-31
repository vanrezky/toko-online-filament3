<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #374151; 
            background-color: #f3f4f6; 
            margin: 0;
            padding: 20px;
        }
        .email-wrapper { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
        }
        .email-header { 
            padding: 30px 40px; 
            text-align: center; 
            background: linear-gradient(135deg, {{ $headerGradient ?? '#4F46E5 0%, #7C3AED 100%' }}); 
        }
        .email-header img { 
            max-width: 150px; 
            max-height: 50px; 
            margin-bottom: 10px; 
        }
        .email-header h1 { 
            color: #ffffff; 
            font-size: 24px; 
            font-weight: 600; 
            margin: 0; 
        }
        .email-content { 
            padding: 40px; 
        }
        .email-greeting { 
            font-size: 16px; 
            margin-bottom: 20px; 
        }
        .email-greeting strong { 
            color: #4F46E5; 
        }
        .email-paragraph { 
            margin-bottom: 16px; 
            color: #374151; 
        }
        .email-box { 
            background-color: #f9fafb; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0; 
        }
        .email-box p { 
            margin: 8px 0; 
        }
        .email-box p strong { 
            color: #374151; 
            min-width: 140px; 
            display: inline-block; 
        }
        .email-button { 
            display: inline-block; 
            padding: 14px 32px; 
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            margin: 20px 0; 
            text-align: center; 
        }
        .email-button:hover { 
            background: linear-gradient(135deg, #4338CA 0%, #6D28D9 100%); 
        }
        .email-warning { 
            background-color: #FEF3C7; 
            border: 1px solid #F59E0B; 
            border-radius: 8px; 
            padding: 16px; 
            margin: 20px 0; 
        }
        .email-warning p { 
            color: #92400E; 
            margin: 4px 0; 
        }
        .email-success { 
            background-color: #D1FAE5; 
            border: 1px solid #10B981; 
            border-radius: 8px; 
            padding: 16px; 
            margin: 20px 0; 
        }
        .email-success p { 
            color: #065F46; 
            margin: 4px 0; 
        }
        .email-error { 
            background-color: #FEE2E2; 
            border: 1px solid #EF4444; 
            border-radius: 8px; 
            padding: 16px; 
            margin: 20px 0; 
        }
        .email-error p { 
            color: #991B1B; 
            margin: 4px 0; 
        }
        .email-footer { 
            text-align: center; 
            padding: 30px 40px; 
            background-color: #f9fafb; 
            border-top: 1px solid #e5e7eb; 
        }
        .email-footer p { 
            color: #6B7280; 
            font-size: 14px; 
            margin: 4px 0; 
        }
        .email-footer a { 
            color: #4F46E5; 
            text-decoration: none; 
        }
        .status-badge { 
            display: inline-block; 
            padding: 6px 16px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 14px; 
        }
        .status-old { 
            background-color: #E5E7EB; 
            color: #6B7280; 
        }
        .status-new { 
            background-color: #D1FAE5; 
            color: #065F46; 
        }
        .text-center { text-align: center; }
        .text-muted { color: #6B7280; }
        .text-sm { font-size: 14px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mb-4 { margin-bottom: 16px; }
        @media only screen and (max-width: 600px) {
            .email-wrapper { border-radius: 0; }
            .email-content { padding: 20px; }
            .email-header { padding: 20px; }
            .email-header img { max-width: 120px; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $websiteName }}">
            @endif
            @isset($headerTitle)
                <h1>{{ $headerTitle }}</h1>
            @endisset
        </div>
        <div class="email-content">
            {!! $content !!}
        </div>
        <div class="email-footer">
            <p><strong>{{ $websiteName }}</strong></p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>