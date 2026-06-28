@php
    use Mca\Settings\Support\ContactPhonesField;

    $oldSettings = old('settings', []);
    $phones = is_array($oldSettings[ContactPhonesField::KEY] ?? null)
        ? $oldSettings[ContactPhonesField::KEY]
        : ($phones ?? []);
    if (! is_array($phones)) {
        $phones = [];
    }
    if ($phones === []) {
        $phones = [ContactPhonesField::emptyRow()];
    }
    $fieldKey = ContactPhonesField::KEY;
    $types = ContactPhonesField::types();
@endphp

<div class="mca-sett-repeater" data-mca-sett-repeater data-mca-sett-repeater-template="mcaSettContactPhoneTemplate">
    <div class="mca-sett-repeater__head">
        <span class="mca-sett-repeater__title">{{ mca_sett('phones.title') }}</span>
    </div>

    <div class="mca-sett-repeater__table-wrap">
        <table class="mca-sett-repeater__table mca-sett-repeater__table--phones">
            <thead>
                <tr>
                    <th>{{ mca_sett('phones.label') }}</th>
                    <th>{{ mca_sett('phones.number') }}</th>
                    <th>{{ mca_sett('phones.type') }}</th>
                    <th class="mca-sett-repeater__col-action">{{ mca_sett('phones.action') }}</th>
                </tr>
            </thead>
            <tbody data-mca-sett-repeater-body>
                @foreach ($phones as $index => $phone)
                    @include('mca-settings::partials.fields.contact-phones-row', [
                        'index' => $index,
                        'phone' => is_array($phone) ? $phone : ContactPhonesField::emptyRow(),
                        'fieldKey' => $fieldKey,
                        'types' => $types,
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mca-sett-repeater__footer">
        <button type="button" class="mca-perm-btn mca-perm-btn--primary mca-sett-repeater__add" data-mca-sett-repeater-add>
            + {{ mca_sett('phones.add') }}
        </button>
        <p class="mca-perm-help mca-sett-repeater__hint">{{ mca_sett('phones.hint') }}</p>
    </div>
</div>

<template id="mcaSettContactPhoneTemplate">
    @include('mca-settings::partials.fields.contact-phones-row', [
        'index' => '__INDEX__',
        'phone' => ContactPhonesField::emptyRow(),
        'fieldKey' => $fieldKey,
        'types' => $types,
    ])
</template>
