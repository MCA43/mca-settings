<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::uiCssUrl() }}">
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::cssUrl() }}">
</head>
<body class="mca-ui-root mca-sett-root mca-sett-maint">
    <main class="mca-sett-maint__wrap">
        @if ($image)
            <img src="{{ asset($image) }}" alt="" class="mca-sett-maint__img">
        @endif
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
