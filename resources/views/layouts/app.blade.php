<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $mcaSettTitle ?? mca_sett('app.title'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::uiCssUrl() }}">
    <link rel="stylesheet" href="{{ \Mca\Settings\Support\McaSettingsView::cssUrl() }}">
    @stack('mca-sett-head')
</head>
<body class="mca-ui-root mca-perm-root mca-sett-root">
    @include('mca-settings::partials.header', [
        'groups' => $groups ?? [],
        'activeGroup' => $activeGroup ?? request('group'),
    ])

    <main class="mca-ui-main mca-perm-main mca-sett-main">
        @include('mca-settings::partials.flash')
        @yield('content')
    </main>

    @php
        $mcaUiI18n = [
            'ok' => mca_sett('modal.ok'),
            'confirm' => mca_sett('modal.confirm'),
            'cancel' => mca_sett('modal.cancel'),
            'close' => mca_sett('modal.close'),
            'alert_title' => mca_sett('modal.alert_title'),
            'confirm_title' => mca_sett('modal.confirm_title'),
        ];
    @endphp
    <script>
        window.McaUiI18n = @json($mcaUiI18n);
    </script>
    <script src="{{ \Mca\Settings\Support\McaSettingsView::uiJsUrl() }}" defer></script>
    <script src="{{ \Mca\Settings\Support\McaSettingsView::jsUrl() }}" defer></script>
    @stack('mca-sett-scripts')
</body>
</html>
