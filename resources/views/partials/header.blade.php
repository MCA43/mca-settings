@php
    $np = config('settings.routes.web.name_prefix', 'mca.settings.');
    $groupList = $groups ?? [];
    $currentGroup = $activeGroup ?? request('group', $groupList[0] ?? 'general');
@endphp
<header class="mca-ui-shell" id="mcaUiShell">
    <div class="mca-ui-shell__wrap">
        <div class="mca-ui-shell__inner">
            <a href="{{ route($np.'index', ['group' => $currentGroup]) }}" class="mca-ui-brand">
                <span class="mca-ui-brand__mark" aria-hidden="true">
                    @include('mca-settings::partials.icon', ['name' => 'cog'])
                </span>
                <span>{{ $mcaSettTitle ?? mca_sett('app.brand') }}</span>
            </a>

            <button type="button"
                    class="mca-ui-menu-btn"
                    id="mcaUiMenuBtn"
                    aria-expanded="false"
                    aria-controls="mcaUiNav"
                    aria-label="{{ mca_sett('app.nav_aria') }}">
                @include('mca-settings::partials.icon', ['name' => 'menu'])
            </button>
        </div>

        <nav class="mca-ui-nav" id="mcaUiNav" aria-label="{{ mca_sett('app.nav_aria') }}">
            @if(Route::has('mca.hub.index'))
                <a href="{{ route('mca.hub.index') }}" class="mca-ui-nav__link">
                    @include('mca-settings::partials.icon', ['name' => 'grid', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                    {{ mca_sett('nav.back_mca') }}
                </a>
            @endif

            @foreach($groupList as $group)
                <a href="{{ route($np.'index', ['group' => $group]) }}"
                   class="mca-ui-nav__link {{ $currentGroup === $group ? 'mca-ui-nav__link--active' : '' }}">
                    {{ \Mca\Settings\Support\McaSettingsView::groupLabel((string) $group) }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
