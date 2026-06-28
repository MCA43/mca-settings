@php
    use Mca\Settings\Support\ContactMapField;

    $settings = app(\Mca\Settings\Services\SettingsService::class);
    $oldSettings = old('settings', []);

    $embed = is_string($oldSettings[ContactMapField::EMBED_KEY] ?? null)
        ? $oldSettings[ContactMapField::EMBED_KEY]
        : ContactMapField::embedHtml($settings);
    $lat = is_string($oldSettings[ContactMapField::LAT_KEY] ?? null) || is_numeric($oldSettings[ContactMapField::LAT_KEY] ?? null)
        ? (string) $oldSettings[ContactMapField::LAT_KEY]
        : (string) ($settings->get(ContactMapField::LAT_KEY, '') ?? '');
    $lng = is_string($oldSettings[ContactMapField::LNG_KEY] ?? null) || is_numeric($oldSettings[ContactMapField::LNG_KEY] ?? null)
        ? (string) $oldSettings[ContactMapField::LNG_KEY]
        : (string) ($settings->get(ContactMapField::LNG_KEY, '') ?? '');
@endphp

<div class="mca-sett-map">
    <div class="mca-sett-map__head">
        <span class="mca-sett-map__title">{{ mca_sett('map.title') }}</span>
        <p class="mca-perm-help mca-sett-map__priority">{{ mca_sett('map.priority') }}</p>
    </div>

    <div class="mca-sett-map__section">
        <label class="mca-perm-label" for="sett-map-embed">{{ mca_sett('map.embed') }}</label>
        <textarea id="sett-map-embed"
                  name="settings[{{ ContactMapField::EMBED_KEY }}]"
                  class="mca-perm-input mca-sett-textarea"
                  rows="4"
                  placeholder="<iframe ...></iframe>">{{ $embed }}</textarea>
        <p class="mca-perm-help">{{ mca_sett('map.embed_hint') }}</p>
    </div>

    <div class="mca-sett-map__divider">
        <span>{{ mca_sett('map.or_coordinates') }}</span>
    </div>

    <div class="mca-sett-map__coords">
        <div class="mca-perm-field">
            <label class="mca-perm-label" for="sett-map-lat">{{ mca_sett('map.latitude') }}</label>
            <input type="number"
                   id="sett-map-lat"
                   name="settings[{{ ContactMapField::LAT_KEY }}]"
                   class="mca-perm-input"
                   value="{{ $lat }}"
                   step="any"
                   placeholder="41.0972">
        </div>
        <div class="mca-perm-field">
            <label class="mca-perm-label" for="sett-map-lng">{{ mca_sett('map.longitude') }}</label>
            <input type="number"
                   id="sett-map-lng"
                   name="settings[{{ ContactMapField::LNG_KEY }}]"
                   class="mca-perm-input"
                   value="{{ $lng }}"
                   step="any"
                   placeholder="28.8678">
        </div>
    </div>

    <p class="mca-perm-help mca-sett-map__footer-hint">{{ mca_sett('map.coords_hint') }}</p>
</div>
