@php
    $row = is_array($link) ? $link : [];
    $oldSettings = old('settings', []);
    $oldRow = is_array($oldSettings[$fieldKey] ?? null) ? ($oldSettings[$fieldKey][$index] ?? []) : [];
    $icon = is_array($oldRow) ? trim((string) ($oldRow['icon'] ?? '')) : '';
    $name = is_array($oldRow) ? trim((string) ($oldRow['name'] ?? '')) : '';
    $href = is_array($oldRow) ? trim((string) ($oldRow['link'] ?? $oldRow['url'] ?? '')) : '';

    if ($icon === '' && $name === '' && $href === '') {
        $icon = trim((string) ($row['icon'] ?? ''));
        $name = trim((string) ($row['name'] ?? ''));
        $href = trim((string) ($row['link'] ?? $row['url'] ?? ''));
    }
@endphp

<tr data-mca-sett-repeater-row>
    <td class="mca-sett-repeater__col-icon">
        <div class="mca-sett-repeater__icon-field">
            <span class="mca-sett-repeater__icon-preview" data-mca-sett-icon-preview aria-hidden="true">
                @if ($icon !== '')
                    <i class="{{ $icon }}"></i>
                @endif
            </span>
            <input type="text"
                   name="settings[{{ $fieldKey }}][{{ $index }}][icon]"
                   class="mca-perm-input"
                   value="{{ $icon }}"
                   placeholder="fab fa-instagram"
                   data-mca-sett-icon-input>
        </div>
    </td>
    <td>
        <input type="text"
               name="settings[{{ $fieldKey }}][{{ $index }}][name]"
               class="mca-perm-input"
               value="{{ $name }}"
               placeholder="Instagram">
    </td>
    <td>
        <input type="url"
               name="settings[{{ $fieldKey }}][{{ $index }}][link]"
               class="mca-perm-input"
               value="{{ $href }}"
               placeholder="https://instagram.com/isim">
    </td>
    <td class="mca-sett-repeater__col-action">
        <button type="button"
                class="mca-sett-repeater__remove"
                data-mca-sett-repeater-remove
                title="{{ mca_sett('social.remove') }}"
                aria-label="{{ mca_sett('social.remove') }}">
            @include('mca-settings::partials.icon', ['name' => 'trash', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
        </button>
    </td>
</tr>
