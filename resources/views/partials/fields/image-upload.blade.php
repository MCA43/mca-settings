@php
    $fieldId = 'sett-'.md5($key);
    $stored = old('settings.'.$key, is_string($value) ? $value : '');
    $aspect = str_contains((string) $key, 'favicon') ? 'square' : 'wide';
@endphp

@if (\Mca\Settings\Support\ImageUploadField::isAvailable())
    {{-- Keep current path when no new file is chosen --}}
    <input type="hidden" name="settings[{{ $key }}]" value="{{ $stored }}">

    <x-mca-upload::image-field
        :id="$fieldId"
        name="uploads[{{ $key }}]"
        :value="is_string($stored) && $stored !== '' ? $stored : null"
        :preset="$key"
        :preserve="false"
        :aspect="$aspect"
    />
    <p class="mca-perm-help">{{ mca_sett('form.image_upload_hint') }}</p>
@else
    <input type="text"
           id="{{ $fieldId }}"
           name="settings[{{ $key }}]"
           class="mca-perm-input mca-perm-mono"
           value="{{ $stored }}"
           placeholder="uploads/branding/logo.png">
    <p class="mca-perm-help">{{ mca_sett('form.image_upload_fallback_hint') }}</p>
@endif
