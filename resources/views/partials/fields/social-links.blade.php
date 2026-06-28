@php
    use Mca\Settings\Support\SocialLinksField;

    $oldSettings = old('settings', []);
    $links = is_array($oldSettings[SocialLinksField::KEY] ?? null)
        ? $oldSettings[SocialLinksField::KEY]
        : ($links ?? []);
    if (! is_array($links)) {
        $links = [];
    }
    if ($links === []) {
        $links = [SocialLinksField::emptyRow()];
    }
    $fieldKey = SocialLinksField::KEY;
@endphp

@push('mca-sett-head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
@endpush

<div class="mca-sett-repeater" data-mca-sett-repeater data-mca-sett-repeater-template="mcaSettSocialLinkTemplate">
    <div class="mca-sett-repeater__head">
        <span class="mca-sett-repeater__title">{{ mca_sett('social.title') }}</span>
    </div>

    <div class="mca-sett-repeater__table-wrap">
        <table class="mca-sett-repeater__table">
            <thead>
                <tr>
                    <th>{{ mca_sett('social.icon') }}</th>
                    <th>{{ mca_sett('social.name') }}</th>
                    <th>{{ mca_sett('social.link') }}</th>
                    <th class="mca-sett-repeater__col-action">{{ mca_sett('social.action') }}</th>
                </tr>
            </thead>
            <tbody data-mca-sett-repeater-body>
                @foreach ($links as $index => $link)
                    @include('mca-settings::partials.fields.social-links-row', [
                        'index' => $index,
                        'link' => is_array($link) ? $link : SocialLinksField::emptyRow(),
                        'fieldKey' => $fieldKey,
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mca-sett-repeater__footer">
        <button type="button" class="mca-perm-btn mca-perm-btn--primary mca-sett-repeater__add" data-mca-sett-repeater-add>
            + {{ mca_sett('social.add') }}
        </button>
        <p class="mca-perm-help mca-sett-repeater__hint">{!! mca_sett('social.hint') !!}</p>
    </div>
</div>

<template id="mcaSettSocialLinkTemplate">
    @include('mca-settings::partials.fields.social-links-row', [
        'index' => '__INDEX__',
        'link' => SocialLinksField::emptyRow(),
        'fieldKey' => $fieldKey,
    ])
</template>
