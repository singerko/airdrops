<!-- resources/views/emails/airdrop-notification.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? 'Airdrop Notification' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .notification-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .type-new_phase {
            background: #e1f5fe;
            color: #0277bd;
        }
        .type-deadline_reminder {
            background: #fff3e0;
            color: #f57c00;
        }
        .type-status_change {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .type-airdrop_ending {
            background: #ffebee;
            color: #c62828;
        }
        .airdrop-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .airdrop-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 8px 0;
        }
        .airdrop-meta {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 AirdropPortal</h1>
        </div>
        
        <div class="content">
            <span class="notification-type type-{{ $type }}">
                {{ str_replace('_', ' ', $type) }}
            </span>
            
            <h2>Hello {{ $user->name }}!</h2>
            
            @if($type === 'new_phase')
                <p>Great news! A new phase has been added to one of your subscribed airdrops.</p>
            @elseif($type === 'deadline_reminder')
                <p>⏰ Don't forget! One of your subscribed airdrops is ending soon.</p>
            @elseif($type === 'status_change')
                <p>📢 There's been a status update for one of your subscribed airdrops.</p>
            @elseif($type === 'airdrop_ending')
                <p>⚡ Last chance! One of your subscribed airdrops is ending very soon.</p>
            @else
                <p>There's an update for one of your subscribed airdrops.</p>
            @endif

            <div class="airdrop-info">
                <div class="airdrop-title">{{ $airdrop->title }}</div>
                <div class="airdrop-meta">
                    {{ $airdrop->project->name }} • {{ $airdrop->blockchain->name }}
                    @if($airdrop->estimated_value)
                        • Est. Value: ${{ number_format($airdrop->estimated_value, 0) }}
                    @endif
                </div>
                
                @if($type === 'deadline_reminder' || $type === 'airdrop_ending')
                    @if($airdrop->ends_at)
                        <p><strong>Ends:</strong> {{ $airdrop->ends_at->format('F j, Y \a\t g:i A T') }}</p>
                    @endif
                @endif

                @if($airdrop->description)
                    <p>{{ Str::limit($airdrop->description, 200) }}</p>
                @endif
            </div>

            <a href="{{ $actionUrl }}" class="button">View Airdrop Details</a>

            <p>Stay up to date with the latest opportunities and never miss an airdrop!</p>
        </div>

        <div class="footer">
            <p>
                You're receiving this because you subscribed to notifications for this airdrop.<br>
                <a href="{{ route('profile.index') }}">Manage your notification preferences</a>
            </p>
            <p>© {{ date('Y') }} AirdropPortal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
