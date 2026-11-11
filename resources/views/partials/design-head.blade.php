<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Fraunces"', 'Georgia', '"Times New Roman"', 'serif'],
                    mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                },
                borderRadius: {
                    xl: '0.9rem',
                    '2xl': '1.1rem',
                    '3xl': '1.4rem',
                },
                boxShadow: {
                    card: '0 1px 2px rgba(16,24,40,0.04)',
                    'card-hover': '0 6px 18px -8px rgba(16,24,40,0.18)',
                },
                colors: {
                    gold: {
                        200: '#f2e4c4',
                        300: '#e6c98a',
                        400: '#d9b468',
                        500: '#ba812c',
                        600: '#9a6a22',
                        700: '#7c551b',
                    },
                    ink: {
                        700: '#27405f',
                        800: '#1f3350',
                        900: '#1b2e46',
                        950: '#101823',
                    },
                    blue: {
                        50: '#f4f6fa',
                        100: '#e6ebf3',
                        200: '#cbd6e6',
                        300: '#a3b6d1',
                        400: '#6e8cb4',
                        500: '#456894',
                        600: '#2f5078',
                        700: '#27405f',
                        800: '#1f3350',
                        900: '#1b2e46',
                        950: '#101823',
                    },
                    slate: {
                        50: '#fcfaf6',
                        100: '#f4f0e8',
                        200: '#e6e0d4',
                        300: '#d4ccbd',
                        400: '#aba59a',
                        500: '#6b7280',
                        600: '#545c6b',
                        700: '#39414f',
                        800: '#232a36',
                        900: '#161b22',
                        950: '#0b0d10',
                    },
                },
            }
        }
    }
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,500&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    code, .font-mono { font-family: 'JetBrains Mono', monospace; }

    .font-display {
        font-family: 'Fraunces', Georgia, 'Times New Roman', serif;
        font-optical-sizing: auto;
        font-weight: 600;
        letter-spacing: -0.015em;
    }

    /* Label kategori navigasi: kapital kecil dengan jarak huruf lebar */
    .label-caps {
        text-transform: uppercase;
        letter-spacing: 0.2em;
        font-size: 10px;
        font-weight: 600;
        color: #545c6b;
    }
    .dark .label-caps { color: #9ca3af; }

    /* Sidebar & drawer selalu berlatar navy gelap, apa pun temanya */
    aside .label-caps,
    #navDrawer .label-caps { color: #9ca3af; }

    /* Kartu konten standar: border halus dengan bayangan sangat tipis */
    .portal-card {
        border-radius: 0.9rem;
        border: 1px solid #e6e0d4;
        background-color: #ffffff;
        box-shadow: 0 1px 2px rgba(16,24,40,0.04);
    }
    .dark .portal-card {
        border-color: rgba(255,255,255,0.14);
        background-color: #1e2128;
        box-shadow: none;
    }

    /* Garis pemisah daftar feed (pengumuman & agenda) */
    .feed-divide > * + * { border-top: 1px solid rgba(230,224,212,0.75); }
    .dark .feed-divide > * + * { border-top-color: rgba(35,42,54,0.9); }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .portal-scroll::-webkit-scrollbar { width: 8px; }
    .portal-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); border-radius: 99px; }
</style>
<script>
    if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }
</script>
