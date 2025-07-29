<!-- resources/views/emails/weekly-digest.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weekly Airdrop Digest</title>
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
        .airdrop-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafafa;
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
            margin-bottom: 12px;
        }
        .airdrop-description {
            color: #4a5568;
            margin-bottom: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active {
            background: #c6f6d5;
            color: #22543d;
        }
        .status-upcoming {
            background: #bee3f8;
            color: #2a4365;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
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
            <h1>📬 Weekly Airdrop Digest</h1>
            <p>{{ now()->format('F j, Y') }}</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            
            <p>Here are the latest airdrops from this week that match your preferences:</p>

            @foreach($airdrops as $airdrop)
                <div class="airdrop-item">
                    <div class="airdrop-title">{{ $airdrop->title }}</div>
                    <div class="airdrop-meta">
                        {{ $airdrop->project->name }} • {{ $airdrop->blockchain->name }}
                        @if($airdrop->estimated_value)
                            • Est. Value: ${{ number_format($airdrop->estimated_value, 0) }}
                        @endif
                    </div>
                    
                    @if($airdrop->description)
                        <div class="airdrop-description">
                            {{ Str::limit($airdrop->description, 150) }}
                        </div>
                    @endif

                    <div style="margin-bottom: 12px;">
                        <span class="status-badge status-{{ $airdrop->status }}">
                            {{ ucfirst($airdrop->status) }}
                        </span>
                    </div>

                    <a href="{{ route('airdrops.show', $airdrop->slug) }}" class="button">View Details</a>
                </div>
            @endforeach

            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f0f4f8; border-radius: 8px;">
                <p><strong>Want to see more airdrops?</strong></p>
                <a href="{{ route('airdrops.index') }}" class="button">Browse All Airdrops</a>
            </div>
        </div>

        <div class="footer">
            <p>
                You're receiving this weekly digest based on your preferences.<br>
                <a href="{{ $unsubscribeUrl }}">Update your notification settings</a>
            </p>
            <p>© {{ date('Y') }} AirdropPortal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
