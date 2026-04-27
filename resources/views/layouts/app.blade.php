<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | BlassaCar' : 'BlassaCar' }}</title>
    <meta name="description" content="BlassaCar is a Moroccan carpooling platform for safer, simpler, and more affordable intercity travel.">
    
    <!-- Fallback for when Vite is not running -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f2fbff',
                            100: '#dff5ff',
                            200: '#b8e8fb',
                            300: '#84d6f3',
                            400: '#46bee7',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#0a4f74',
                            900: '#0a3448',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            * {
                @apply border-slate-200;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                @apply bg-slate-50 font-sans text-slate-900 antialiased;
                background-image:
                    radial-gradient(circle at top left, rgba(14, 165, 233, 0.1), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(2, 132, 199, 0.08), transparent 18%);
                background-attachment: fixed;
            }

            ::selection {
                background: rgba(14, 165, 233, 0.2);
            }
        }

        @layer components {
            .shell {
                @apply mx-auto max-w-7xl px-4 sm:px-6 lg:px-8;
            }

            .surface {
                @apply min-w-0 rounded-[2rem] border border-white/70 bg-white/90 shadow-[0_22px_70px_-40px_rgba(14,165,233,0.65)] backdrop-blur;
            }

            .surface-soft {
                @apply min-w-0 rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_18px_55px_-42px_rgba(15,23,42,0.45)];
            }

            .page-enter {
                animation: page-enter 0.55s cubic-bezier(0.2, 0.8, 0.2, 1) both;
            }

            .hero-grid {
                background-image:
                    linear-gradient(to right, rgba(14, 165, 233, 0.09) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(14, 165, 233, 0.09) 1px, transparent 1px);
                background-size: 2.8rem 2.8rem;
                mask-image: radial-gradient(circle at center, black 42%, transparent 88%);
            }

            .hero-image-panel {
                width: 49.5%;
                overflow: hidden;
                clip-path: ellipse(92% 112% at 100% 48%);
            }

            .brand-button {
                @apply inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 px-5 py-3 font-semibold text-white transition duration-200 hover:bg-brand-700;
            }

            .brand-button-secondary {
                @apply inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-3 font-semibold text-slate-700 transition duration-200 hover:border-brand-200 hover:text-brand-700;
            }

            .nav-link {
                @apply inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-brand-700;
            }

            .nav-link-active {
                @apply bg-white text-brand-700 shadow-sm;
            }

            .input-shell {
                @apply flex items-center gap-3 rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3 transition focus-within:border-brand-400 focus-within:bg-white focus-within:shadow-[0_0_0_6px_rgba(14,165,233,0.10)];
            }

            .date-input-clean {
                appearance: none;
            }

            .date-input-clean::-webkit-calendar-picker-indicator {
                display: none;
                -webkit-appearance: none;
            }

            .date-input-clean::-webkit-clear-button,
            .date-input-clean::-webkit-inner-spin-button {
                display: none;
            }

            .stat-tile {
                @apply rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_55px_-42px_rgba(15,23,42,0.45)];
            }

            .dashboard-panel {
                @apply min-w-0 rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-[0_18px_55px_-42px_rgba(15,23,42,0.45)];
            }
        }

        @keyframes page-enter {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="{{ $bodyClass ?? '' }}">
    <div class="min-h-screen">
        @if (($showHeader ?? true) === true)
            @include('partials.header')
        @endif

        @if (session('status'))
            <div class="shell pt-6">
                <div class="rounded-[1.5rem] border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-medium text-brand-800">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @yield('content')

        @if (($showFooter ?? true) === true)
            @include('partials.footer')
        @endif
    </div>
</body>
</html>
