<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Source Serif 4"', 'Georgia', 'serif'],
                    mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                },
                colors: {
                    gold: {
                        300: '#e6d3a3',
                        400: '#d4b483',
                        500: '#b08948',
                        600: '#8d6b34',
                    },
                    ink: {
                        800: '#16324a',
                        900: '#102433',
                        950: '#0b1a26',
                    },
                    blue: {
                        50: '#f3f7fb',
                        100: '#e5f0f8',
                        200: '#c6deee',
                        300: '#96c3dc',
                        400: '#5e9dc4',
                        500: '#3a7eaa',
                        600: '#1f628c',
                        700: '#1a5072',
                        800: '#18425e',
                        900: '#16384f',
                        950: '#0e2435',
                    },
                    slate: {
                        50: '#f6f4ef',
                        100: '#eeeae3',
                        200: '#e3ddd3',
                        800: '#24303c',
                        900: '#151c25',
                        950: '#0c1218',
                    },
                },
            }
        }
    }
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,500&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    .font-display { font-family: 'Source Serif 4', Georgia, serif; letter-spacing: -0.02em; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .portal-scroll::-webkit-scrollbar { width: 8px; }
    .portal-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 99px; }
</style>
<script>
    if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }
</script>
