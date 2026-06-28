@php
    $row = is_array($phone) ? $phone : [];
    $oldSettings = old('settings', []);
    $oldRow = is_array($oldSettings[$fieldKey] ?? null) ? ($oldSettings[$fieldKey][$index] ?? []) : [];
    $label = is_array($oldRow) ? trim((string) ($oldRow['label'] ?? '')) : '';
    $number = is_array($oldRow) ? trim((string) ($oldRow['number'] ?? '')) : '';
    $type = is_array($oldRow) ? trim((string) ($oldRow['type'] ?? 'phone')) : 'phone';

    if ($label === '' && $number === '') {
        $label = trim((string) ($row['label'] ?? ''));
        $number = trim((string) ($row['number'] ?? ''));
        $type = trim((string) ($row['type'] ?? 'phone'));
    }
@endphp

<tr data-mca-sett-repeater-row>
    <td>
        <input type="text"
               name="settings[{{ $fieldKey }}][{{ $index }}][label]"
               class="mca-perm-input"
               value="{{ $label }}"
               placeholder="{{ mca_sett('phones.label_placeholder') }}">
    </td>
    <td>
        <input type="text"
               name="settings[{{ $fieldKey }}][{{ $index }}][number]"
               class="mca-perm-input"
               value="{{ $number }}"
               placeholder="+90 212 000 00 00"
               inputmode="tel"
               autocomplete="tel">
    </td>
    <td>
        <select name="settings[{{ $fieldKey }}][{{ $index }}][type]" class="mca-perm-input">
            @foreach ($types as $typeKey => $typeLabel)
                <option value="{{ $typeKey }}" @selected($type === $typeKey)>{{ mca_sett('phones.types.'.$typeKey) }}</option>
            @endforeach
        </select>
    </td>
    <td class="mca-sett-repeater__col-action">
        <button type="button"
                class="mca-sett-repeater__remove"
                data-mca-sett-repeater-remove
                title="{{ mca_sett('phones.remove') }}"
                aria-label="{{ mca_sett('phones.remove') }}">
            @include('mca-settings::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
        </button>
    </td>
</tr>
