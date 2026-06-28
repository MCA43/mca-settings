@php
    $row = is_array($email) ? $email : [];
    $oldSettings = old('settings', []);
    $oldRow = is_array($oldSettings[$fieldKey] ?? null) ? ($oldSettings[$fieldKey][$index] ?? []) : [];
    $label = is_array($oldRow) ? trim((string) ($oldRow['label'] ?? '')) : '';
    $address = is_array($oldRow) ? trim((string) ($oldRow['email'] ?? '')) : '';
    $type = is_array($oldRow) ? trim((string) ($oldRow['type'] ?? 'contact')) : 'contact';

    if ($label === '' && $address === '') {
        $label = trim((string) ($row['label'] ?? ''));
        $address = trim((string) ($row['email'] ?? ''));
        $type = trim((string) ($row['type'] ?? 'contact'));
    }
@endphp

<tr data-mca-sett-repeater-row>
    <td>
        <input type="text"
               name="settings[{{ $fieldKey }}][{{ $index }}][label]"
               class="mca-perm-input"
               value="{{ $label }}"
               placeholder="{{ mca_sett('emails.label_placeholder') }}">
    </td>
    <td>
        <input type="email"
               name="settings[{{ $fieldKey }}][{{ $index }}][email]"
               class="mca-perm-input"
               value="{{ $address }}"
               placeholder="info@example.com">
    </td>
    <td>
        <select name="settings[{{ $fieldKey }}][{{ $index }}][type]" class="mca-perm-input">
            @foreach ($types as $typeKey => $typeLabel)
                <option value="{{ $typeKey }}" @selected($type === $typeKey)>{{ mca_sett('emails.types.'.$typeKey) }}</option>
            @endforeach
        </select>
    </td>
    <td class="mca-sett-repeater__col-action">
        <button type="button"
                class="mca-sett-repeater__remove"
                data-mca-sett-repeater-remove
                title="{{ mca_sett('emails.remove') }}"
                aria-label="{{ mca_sett('emails.remove') }}">
            @include('mca-settings::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
        </button>
    </td>
</tr>
