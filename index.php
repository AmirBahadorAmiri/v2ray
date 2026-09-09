<?php
function parseConfigInfo($config)
{
    $protocol = 'UNKNOWN';
    $host = 'نامشخص';
    $port = '-';

    if (preg_match('/^(vless|trojan|vmess|ss|http|https):\/\//i', $config, $matches)) {
        $protocol = strtoupper($matches[1]);
    }

    if (preg_match('/^(?:vless|trojan|vmess|ss|http|https):\/\/[^@]*@([^\/:?#]+)(?::(\d+))?/i', $config, $matches)) {
        $host = $matches[1];
        $port = $matches[2] ?? '-';
    }

    $hostDisplay = $host;
    if (strlen($hostDisplay) > 24) {
        $hostDisplay = substr($hostDisplay, 0, 24) . '…';
    }

    return [
        'protocol' => $protocol,
        'host' => $host,
        'host_display' => $hostDisplay,
        'port' => $port,
        'config' => $config,
    ];
}

//$subUrl = "https://raw.githubusercontent.com/AmirBahadorAmiri/v2raysub/refs/heads/main/sub.txt";
$subUrl = "./sub.txt";
$configs = [];

$content = @file_get_contents($subUrl);
if ($content !== false) {
    $lines = explode("\n", trim($content));
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== "") {
            $configs[] = $line;
        }
    }
    shuffle($configs);
    $configs = array_slice($configs, 0, 50);
} else {
    $error = "خطا در دریافت فایل اشتراک.";
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کانفیگ‌های V2ray</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --bg: #020617;
            --surface: #0f172a;
            --surface-2: #111827;
            --text: #f8fafc;
            --muted: #94a3b8;
            --border: #334155;
            --accent: #22d3ee;
            --accent-2: #6366f1;
            --button: #1e293b;
            --button-text: #e2e8f0;
            --card-shadow: rgba(0, 0, 0, 0.3);
        }

        body[data-theme="light"] {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-2: #f1f5f9;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --accent: #0ea5e9;
            --accent-2: #4f46e5;
            --button: #f8fafc;
            --button-text: #0f172a;
            --card-shadow: rgba(15, 23, 42, 0.08);
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: background 0.25s ease, color 0.25s ease;
        }

        .page-shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 20px 40px var(--card-shadow);
        }

        .panel-header {
            background: color-mix(in srgb, var(--surface) 92%, transparent);
            backdrop-filter: blur(8px);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 16px 32px var(--card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 40px var(--card-shadow);
        }

        .meta-box {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 14px;
            min-width: 0;
        }

        .meta-label {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .meta-value {
            font-weight: 700;
            font-size: 14px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            direction: rtl;
            unicode-bidi: plaintext;
        }

        .btn {
            border: none;
            border-radius: 16px;
            padding: 12px 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: white;
        }

        .btn-secondary {
            background: var(--button);
            color: var(--button-text);
            border: 1px solid var(--border);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .pill-vless {
            background: rgba(16, 185, 129, 0.14);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .pill-trojan {
            background: rgba(245, 158, 11, 0.14);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .code-block {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            font-size: 13px;
            line-height: 1.7;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-all;
            color: var(--text);
        }

        .result-box {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--muted);
            white-space: pre-wrap;
        }

        .theme-toggle {
            background: var(--button);
            color: var(--button-text);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 10px 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .sr-only {
            display: none;
        }
    </style>
</head>
<body data-theme="dark">
    <div class="page-shell">
        <header class="panel panel-header p-5 md:p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full bg-slate-900/80 shadow-lg ring-1 ring-white/10">
                        <img src="v2rayn.png" alt="V2Ray logo" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">V2Ray Configs</h1>
                        <p class="text-sm" style="color: var(--muted);">نمایش IP، پورت و نوع اتصال به‌جای آدرس کامل</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button id="themeToggle" class="theme-toggle" type="button">
                        <i id="themeIcon" class="fa-solid fa-moon"></i>
                        <span id="themeLabel">تم تیره</span>
                    </button>

                    <div class="rounded-2xl border px-4 py-3 text-sm" style="border-color: var(--border); background: var(--surface-2); color: var(--muted);">
                        <span class="ml-2 font-semibold" style="color: var(--accent);"><?= count($configs) ?></span>
                        کانفیگ موجود
                    </div>
                </div>
            </div>
        </header>

        <?php if (isset($error)): ?>
            <div class="panel mt-6 p-6 text-center" style="border-color: rgba(239, 68, 68, 0.35); background: rgba(239, 68, 68, 0.1); color: #fda4af;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php else: ?>
            <div class="grid gap-6 lg:grid-cols-2 mt-6">
                <?php foreach ($configs as $index => $config):
                    $info = parseConfigInfo($config);
                    $isVless = stripos($info['protocol'], 'VLESS') !== false;
                    $pillClass = $isVless ? 'pill-vless' : 'pill-trojan';
                ?>
                    <article class="card p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <span class="pill <?= $pillClass ?>">
                                <?= htmlspecialchars($info['protocol']) ?>
                            </span>
                            <span class="text-sm" style="color: var(--muted);">#<?= $index + 1 ?></span>
                        </div>

                        <div class="grid gap-3 md:grid-cols-[1.6fr_0.7fr_1fr] mb-4">
                            <div class="meta-box">
                                <span class="meta-label">IP</span>
                                <div class="meta-value"><?= htmlspecialchars($info['host_display']) ?></div>
                            </div>
                            <div class="meta-box">
                                <span class="meta-label">پورت</span>
                                <div class="meta-value"><?= htmlspecialchars($info['port']) ?></div>
                            </div>
                            <div class="meta-box">
                                <span class="meta-label">نوع اتصال</span>
                                <div class="meta-value"><?= htmlspecialchars($info['protocol']) ?></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-center gap-2 action-row">
                            <button type="button" class="btn btn-secondary copy-btn px-3 py-2 text-sm" data-config="<?= htmlspecialchars($config, ENT_QUOTES) ?>">
                                <i class="fa-solid fa-copy"></i>
                                کپی کانفیگ
                            </button>

                            <button type="button" class="btn btn-secondary px-3 py-2 text-sm" onclick="openV2rayNg('<?= htmlspecialchars($config, ENT_QUOTES) ?>')">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                                V2RayNG
                            </button>

                            <button type="button" class="btn btn-secondary test-btn px-3 py-2 text-sm" data-host="<?= htmlspecialchars($info['host'], ENT_QUOTES) ?>" data-port="<?= htmlspecialchars($info['port'], ENT_QUOTES) ?>">
                                <i class="fa-solid fa-signal"></i>
                                تست اتصال
                            </button>

                            <button type="button" class="btn btn-secondary px-3 py-2 text-sm" onclick="showQR('<?= htmlspecialchars($config, ENT_QUOTES) ?>')">
                                <i class="fa-solid fa-qrcode"></i>
                                QR Code
                            </button>
                        </div>

                        <div class="result-box connectivity-result" style="display:none;"></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="qrModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4">
        <div class="panel p-6 w-full max-w-sm">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-xl font-semibold">QR Code</h3>
                <button type="button" onclick="closeModal()" class="text-2xl" style="color: var(--muted);">×</button>
            </div>
            <div id="qrcode" class="mx-auto flex justify-center rounded-2xl bg-white p-4"></div>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeLabel = document.getElementById('themeLabel');
        const storageKey = 'v2ray-theme';

        function applyTheme(theme) {
            const isDark = theme === 'dark';
            document.body.setAttribute('data-theme', isDark ? 'dark' : 'light');
            themeIcon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            themeLabel.textContent = isDark ? 'تم روشن' : 'تم تیره';
            localStorage.setItem(storageKey, theme);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem(storageKey);
            if (savedTheme) {
                applyTheme(savedTheme);
                return;
            }
            applyTheme(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        }

        themeToggle.addEventListener('click', () => {
            const nextTheme = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });

        initTheme();

        function copyText(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                return navigator.clipboard.writeText(text);
            }

            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.setAttribute('readonly', '');
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.select();
            const copied = document.execCommand('copy');
            document.body.removeChild(textArea);

            if (copied) {
                return Promise.resolve();
            }

            return Promise.reject(new Error('Copy failed'));
        }

        document.querySelectorAll('.copy-btn').forEach((button) => {
            button.addEventListener('click', function () {
                const config = this.dataset.config;
                const original = this.innerHTML;

                copyText(config)
                    .then(() => {
                        this.innerHTML = '<i class="fa-solid fa-check"></i> کپی شد';
                        setTimeout(() => {
                            this.innerHTML = original;
                        }, 1800);
                    })
                    .catch(() => {
                        this.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> خطا در کپی';
                        setTimeout(() => {
                            this.innerHTML = original;
                        }, 1800);
                    });
            });
        });

        document.querySelectorAll('.test-btn').forEach((button) => {
            button.addEventListener('click', function () {
                const host = this.dataset.host;
                const port = this.dataset.port;
                const resultBox = this.closest('.action-row').nextElementSibling;
                const original = this.innerHTML;

                if (!host || port === '-' || port === '') {
                    resultBox.style.display = 'block';
                    resultBox.textContent = '⚠️ برای این کانفیگ IP یا پورت معتبر وجود ندارد.';
                    return;
                }

                resultBox.style.display = 'block';
                resultBox.textContent = 'در حال تست اتصال از سمت مرورگر…';
                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال تست';

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 4000);

                fetch(`http://${host}:${port}/`, {
                    method: 'GET',
                    mode: 'no-cors',
                    signal: controller.signal
                })
                    .then(() => {
                        resultBox.textContent = `✅ اتصال از سمت مرورگر به ${host}:${port} برقرار شد.`;
                    })
                    .catch(() => {
                        resultBox.textContent = `⚠️ اتصال از سمت مرورگر به ${host}:${port} برقرار نشد.`;
                    })
                    .finally(() => {
                        clearTimeout(timeoutId);
                        this.disabled = false;
                        this.innerHTML = original;
                    });
            });
        });

        function openV2rayNg(config) {
            const encoded = encodeURIComponent(config);
            const url = `v2rayng://install-config?url=${encoded}`;
            window.location.href = url;
        }

        function showQR(config) {
            const modal = document.getElementById('qrModal');
            const container = document.getElementById('qrcode');
            container.innerHTML = '';

            new QRCode(container, {
                text: config,
                width: 260,
                height: 260,
                colorDark: '#22d3ee',
                colorLight: '#ffffff'
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('qrModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>