<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title }}{{ ! empty($siteName) ? ' — '.$siteName : '' }}</title>
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::uiCssUrl() }}">
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::cssUrl() }}">
    <script>
        (function () {
            try {
                var dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
                document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            } catch (e) {}
        })();
    </script>
</head>
<body class="mca-ui-root mca-sett-root mca-sett-maint" data-theme>
    <main class="mca-sett-maint__wrap">
        @if (! empty($siteName))
            <p class="mca-sett-maint__brand">{{ $siteName }}</p>
        @endif
        @if ($image)
            <img src="{{ asset($image) }}" alt="" class="mca-sett-maint__img">
        @endif
        <p class="mca-sett-maint__eyebrow">{{ mca_sett('maintenance.default_title') }}</p>
        <h1 class="mca-ui-title">{{ $title }}</h1>
        @if ($message)
            <div class="mca-sett-maint__msg">{!! nl2br(e($message)) !!}</div>
        @endif
        @if ($endDate)
            <p class="mca-sett-maint__end">{{ mca_sett('maintenance.end_label', ['date' => $endDate]) }}</p>
        @endif
    </main>
</body>
</html>
