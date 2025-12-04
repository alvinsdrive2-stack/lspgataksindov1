<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QR Valid - Verifikasi TUK - LSP LPK Gataksindo</title>
    @vite('resources/css/app.css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1a1a1a;
        }
        .header-logo {
    width: 110px;
    height: auto;
    margin: 0 auto 24px;
}

.header-logo-img {
    width: 100%;
    height: auto;
    opacity: 1; /* tidak transparan seperti background */
}

        .container {
            max-width: 360px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.6s ease-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Background */
        .logo-section {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }

        .logo-img {
    width: 100vw;
    height: 100vh;
    object-fit: contain; /* biar nggak ketarik */
    opacity: 0.06;
}

        /* Success Icon */
        .success-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 32px;
        }

        .success-circle {
            width: 48px;
            height: 48px;
            background: #f0f9ff;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            color: #3b82f6;
        }

        /* Title */
        .title {
            margin-bottom: 8px;
        }

        .title h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 32px;
            line-height: 1.4;
        }

        /* Vertical Info Container */
        .info-container {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 0;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .info-item {
            padding: 20px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: background 0.2s ease;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item:hover {
            background: #fafafa;
        }

        .info-icon {
            width: 20px;
            height: 20px;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .info-content {
            text-align: left;
            flex: 1;
        }

        .info-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #1a1a1a;
            line-height: 1.3;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .badge-success {
            background: #f0f9ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
        }

        .badge-warning {
            color: #1e40af;
            border: 1px solid #dbeafe;
        }

        /* Date Section */
        .date-section {
            background: #fafafa;
            border: 1px solid #e5e5e5;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .date-icon {
            width: 20px;
            height: 20px;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .date-content {
            text-align: left;
            flex: 1;
        }

        .date-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .date-value {
            font-size: 16px;
            font-weight: 500;
            color: #1a1a1a;
        }

        /* Buttons */
        .buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #e5e5e5;
            background: #ffffff;
            color: #1a1a1a;
        }

        .btn:hover {
            background: #f9f9f9;
            border-color: #d1d1d1;
        }

        .btn-icon {
            margin-right: 6px;
            font-size: 12px;
        }

        /* Mobile */
        @media (max-width: 640px) {
            .buttons {
                flex-direction: column;
            }

            .container {
                padding: 0 16px;
            }
        }
        .name-section {
    position: relative;
}

.moved-checkmark {
    position: absolute;
    top: 25px;
    right: 17px;
    margin: 0; /* hilangkan margin bawah bawaan */
}

.moved-checkmark .success-circle {
    width: 38px;
    height: 38px;
}

.moved-checkmark .checkmark {
    width: 14px;
    height: 14px;
}

    </style>
</head>
<body>
    <!-- Logo Background -->
    <div class="logo-section">
        <img src="/images/logo-banner.png" alt="LSP Gataksindo" class="logo-img" />
    </div>

    <div class="container">
        <!-- Logo sebagai pengganti checkmark -->
<div class="header-logo">
    <img src="/images/logo-banner.png" alt="LSP Gataksindo" class="header-logo-img" />
</div>

        <!-- Title -->
        <div class="title">
            <h1>Verifikasi Berhasil</h1>
        </div>
        <p class="subtitle">QR Code valid dan terverifikasi</p>

        <!-- Vertical Info Container -->
        <div class="info-container">
            <div class="info-item name-section">
                <div class="success-icon moved-checkmark">
        <div class="success-circle">
            <svg class="checkmark" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Nama</div>
                    <div class="info-value">
                        @if($qrCode->user)
                            {{ $qrCode->user->name }}
                        @else
                            {{ $verification->verificator }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-item">
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Jabatan</div>
                    <div class="info-value">
                        
                            {{ ucfirst($qrCode->type) }}
                        
                    </div>
                </div>
            </div>

            <div class="info-item">
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge {{ $qrCode->status === 'active' ? 'badge-success' : 'badge-success' }}">
                            {{ $qrCode->status === 'active' ? 'Aktif' : 'Terverifikasi' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-item">
                <svg class="info-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <div class="info-content">
                    <div class="info-label">Diverifikasi Pada</div>
                    <div class="info-value">{{ $scannedAt->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        
    </div>
</body>
</html>