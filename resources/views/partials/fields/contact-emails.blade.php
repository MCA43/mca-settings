@php
    use Mca\Settings\Support\ContactEmailsField;

    $oldSettings = old('settings', []);
    $emails = is_array($oldSettings[ContactEmailsField::KEY] ?? null)
        ? $oldSettings[ContactEmailsField::KEY]
        : ($emails ?? []);
    if (! is_array($emails)) {
        $emails = [];
    }
    if ($emails === []) {
        $emails = [ContactEmailsField::emptyRow()];
    }
    $fieldKey = ContactEmailsField::KEY;
    $types = ContactEmailsField::types();
@endphp

<div class="mca-sett-repeater" data-mca-sett-repeater data-mca-sett-repeater-template="mcaSettContactEmailTemplate">
    <div class="mca-sett-repeater__head">
        <span class="mca-sett-repeater__title">{{ mca_sett('emails.title') }}</span>
    </div>

    <div class="mca-sett-repeater__table-wrap">
        <table class="mca-sett-repeater__table mca-sett-repeater__table--emails">
            <thead>
                <tr>
                    <th>{{ mca_sett('emails.label') }}</th>
                    <th>{{ mca_sett('emails.email') }}</th>
                    <th>{{ mca_sett('emails.type') }}</th>
                    <th class="mca-sett-repeater__col-action">{{ mca_sett('emails.action') }}</th>
                </tr>
            </thead>
            <tbody data-mca-sett-repeater-body>
                @foreach ($emails as $index => $email)
                    @include('mca-settings::partials.fields.contact-emails-row', [
                        'index' => $index,
                        'email' => is_array($email) ? $email : ContactEmailsField::emptyRow(),
                        'fieldKey' => $fieldKey,
                        'types' => $types,
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mca-sett-repeater__footer">
        <button type="button" class="mca-perm-btn mca-perm-btn--primary mca-sett-repeater__add" data-mca-sett-repeater-add>
            + {{ mca_sett('emails.add') }}
        </button>
        <p class="mca-perm-help mca-sett-repeater__hint">{{ mca_sett('emails.hint') }}</p>
    </div>
</div>

<template id="mcaSettContactEmailTemplate">
    @include('mca-settings::partials.fields.contact-emails-row', [
        'index' => '__INDEX__',
        'email' => ContactEmailsField::emptyRow(),
        'fieldKey' => $fieldKey,
        'types' => $types,
    ])
</template>
