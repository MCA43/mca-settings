@php
    $fieldId = 'sett-'.md5($key);
    $stored = old('settings.'.$key, $value);
    $roleOptions = \Mca\Settings\Support\PermissionRoleField::options();
    $selected = \Mca\Settings\Support\PermissionRoleField::selectedOptionValue($stored);
@endphp

@if (\Mca\Settings\Support\PermissionRoleField::isAvailable() && $roleOptions !== [])
    <select id="{{ $fieldId }}"
            name="settings[{{ $key }}]"
            class="mca-perm-input">
        <option value="">{{ mca_sett('form.role_placeholder') }}</option>
        @foreach ($roleOptions as $role)
            <option value="{{ $role['id'] }}" @selected((string) $role['id'] === (string) $selected)>
                {{ $role['label'] }}
            </option>
        @endforeach
    </select>
    <p class="mca-perm-help">{{ mca_sett('form.role_select_hint') }}</p>
@else
    <input type="text"
           id="{{ $fieldId }}"
           name="settings[{{ $key }}]"
           class="mca-perm-input mca-perm-mono"
           value="{{ $stored }}"
           placeholder="{{ mca_sett('form.role_input_placeholder') }}">
    <p class="mca-perm-help">{{ mca_sett('form.role_input_hint') }}</p>
@endif
