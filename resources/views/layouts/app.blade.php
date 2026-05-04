<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | BlassaCar' : 'BlassaCar' }}</title>
    <meta name="description" content="BlassaCar is a Moroccan carpooling platform for safer, simpler, and more affordable intercity travel.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Responsive overflow fix */
        html, body { overflow-x: hidden; max-width: 100vw; }
    </style>
</head>
<body class="{{ $bodyClass ?? '' }}" style="overflow-x:hidden;">
    <div class="min-h-screen" style="overflow-x:hidden;">
        @if (($showHeader ?? true) === true)
            @include('partials.header')
        @endif

        @if (session('status'))
            <div class="shell pt-6 flash-message" data-flash-message>
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

    <script>
        document.querySelectorAll('[data-flash-message]').forEach((message) => {
            window.setTimeout(() => {
                message.classList.add('is-hiding');
                window.setTimeout(() => message.remove(), 260);
            }, 3500);
        });
    </script>
</body>
</html>
