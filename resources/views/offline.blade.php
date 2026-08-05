<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1E3A5F">
    <title>Tidak Ada Koneksi — Sistem Absensi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1E3A5F 0%, #2563EB 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            max-width: 380px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        .icon-wrap {
            width: 80px;
            height: 80px;
            background: #EFF6FF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap svg {
            width: 40px;
            height: 40px;
            color: #1E3A5F;
        }
        h1 {
            color: #1E3A5F;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: .75rem;
        }
        p {
            color: #6b7280;
            font-size: .9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: .75rem 1rem;
            text-align: left;
            margin-bottom: 1.5rem;
            font-size: .8rem;
            color: #6b7280;
        }
        .info-box span { display: flex; align-items: center; gap: .5rem; margin-bottom: .25rem; }
        .info-box span:last-child { margin-bottom: 0; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; flex-shrink: 0; }
        button {
            background: #1E3A5F;
            color: white;
            border: none;
            padding: .85rem 1.5rem;
            border-radius: .75rem;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background .2s;
        }
        button:hover { background: #2563EB; }
        button:active { transform: scale(.98); }
        .version { margin-top: 1rem; font-size: .75rem; color: #d1d5db; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>
            </svg>
        </div>

        <h1>Tidak Ada Koneksi</h1>
        <p>Periksa koneksi internet Anda dan coba lagi. Data absensi GPS memerlukan koneksi aktif.</p>

        <div class="info-box">
            <span><span class="dot"></span> Data yang belum tersync akan tersimpan otomatis</span>
            <span><span class="dot" style="background:#10b981"></span> Slip gaji terakhir masih tersedia offline</span>
            <span><span class="dot" style="background:#6366f1"></span> Absensi akan disinkronkan saat kembali online</span>
        </div>

        <button onclick="retryConnection()">
            🔄 Coba Lagi
        </button>

        <p class="version">Sistem Absensi v1.0 &mdash; Offline Mode</p>
    </div>

    <script>
        function retryConnection() {
            if (navigator.onLine) {
                window.location.href = '/employee/attendance';
            } else {
                window.location.reload();
            }
        }

        // Auto retry ketika online
        window.addEventListener('online', () => {
            window.location.href = '/employee/attendance';
        });
    </script>
</body>
</html>