@php
    use Mca\Settings\Support\ContactAddressField;

    $settings = app(\Mca\Settings\Services\SettingsService::class);
    $oldSettings = old('settings', []);
    $defaults = ContactAddressField::valuesForForm($settings);
    $useSelects = ContactAddressField::usesSelectMode();
    $apiRoutes = ContactAddressField::apiRoutes();

    $city = (string) ($oldSettings[ContactAddressField::CITY_KEY] ?? $defaults['city']);
    $district = (string) ($oldSettings[ContactAddressField::DISTRICT_KEY] ?? $defaults['district']);
    $neighborhood = (string) ($oldSettings[ContactAddressField::NEIGHBORHOOD_KEY] ?? $defaults['neighborhood']);
    $cityId = (int) ($oldSettings[ContactAddressField::CITY_ID_KEY] ?? $defaults['city_id']);
    $districtId = (int) ($oldSettings[ContactAddressField::DISTRICT_ID_KEY] ?? $defaults['district_id']);
    $neighborhoodId = (int) ($oldSettings[ContactAddressField::NEIGHBORHOOD_ID_KEY] ?? $defaults['neighborhood_id']);
@endphp

<div class="mca-sett-location"
     data-mca-sett-location
     data-mca-sett-location-mode="{{ $useSelects ? 'select' : 'input' }}"
     @if ($useSelects) data-mca-sett-location-api="{{ json_encode($apiRoutes) }}" @endif>
    <div class="mca-sett-location__head">
        <span class="mca-sett-location__title">{{ mca_sett('location.title') }}</span>
        <p class="mca-perm-help">
            @if ($useSelects)
                {{ mca_sett('location.hint_select') }}
            @else
                {{ mca_sett('location.hint_input') }}
            @endif
        </p>
    </div>

    <div class="mca-sett-location__grid">
        @if ($useSelects)
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-city-id">{{ mca_sett('location.city') }}</label>
                <select id="sett-contact-city-id"
                        name="settings[{{ ContactAddressField::CITY_ID_KEY }}]"
                        class="mca-perm-input"
                        data-mca-sett-location-city>
                    <option value="">{{ mca_sett('location.select_placeholder') }}</option>
                </select>
                <input type="hidden"
                       name="settings[{{ ContactAddressField::CITY_KEY }}]"
                       value="{{ $city }}"
                       data-mca-sett-location-city-name>
            </div>
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-district-id">{{ mca_sett('location.district') }}</label>
                <select id="sett-contact-district-id"
                        name="settings[{{ ContactAddressField::DISTRICT_ID_KEY }}]"
                        class="mca-perm-input"
                        data-mca-sett-location-district
                        @disabled($cityId <= 0)>
                    <option value="">{{ mca_sett('location.select_placeholder') }}</option>
                </select>
                <input type="hidden"
                       name="settings[{{ ContactAddressField::DISTRICT_KEY }}]"
                       value="{{ $district }}"
                       data-mca-sett-location-district-name>
            </div>
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-neighborhood-id">{{ mca_sett('location.neighborhood') }}</label>
                <select id="sett-contact-neighborhood-id"
                        name="settings[{{ ContactAddressField::NEIGHBORHOOD_ID_KEY }}]"
                        class="mca-perm-input"
                        data-mca-sett-location-neighborhood
                        @disabled($districtId <= 0)>
                    <option value="">{{ mca_sett('location.select_placeholder') }}</option>
                </select>
                <input type="hidden"
                       name="settings[{{ ContactAddressField::NEIGHBORHOOD_KEY }}]"
                       value="{{ $neighborhood }}"
                       data-mca-sett-location-neighborhood-name>
            </div>
            <script type="application/json" data-mca-sett-location-initial>
                {!! json_encode([
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'neighborhood_id' => $neighborhoodId,
                ]) !!}
            </script>
        @else
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-city">{{ mca_sett('location.city') }}</label>
                <input type="text"
                       id="sett-contact-city"
                       name="settings[{{ ContactAddressField::CITY_KEY }}]"
                       class="mca-perm-input"
                       value="{{ $city }}"
                       placeholder="{{ mca_sett('location.city_placeholder') }}">
                <input type="hidden" name="settings[{{ ContactAddressField::CITY_ID_KEY }}]" value="0">
            </div>
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-district">{{ mca_sett('location.district') }}</label>
                <input type="text"
                       id="sett-contact-district"
                       name="settings[{{ ContactAddressField::DISTRICT_KEY }}]"
                       class="mca-perm-input"
                       value="{{ $district }}"
                       placeholder="{{ mca_sett('location.district_placeholder') }}">
                <input type="hidden" name="settings[{{ ContactAddressField::DISTRICT_ID_KEY }}]" value="0">
            </div>
            <div class="mca-perm-field">
                <label class="mca-perm-label" for="sett-contact-neighborhood">{{ mca_sett('location.neighborhood') }}</label>
                <input type="text"
                       id="sett-contact-neighborhood"
                       name="settings[{{ ContactAddressField::NEIGHBORHOOD_KEY }}]"
                       class="mca-perm-input"
                       value="{{ $neighborhood }}"
                       placeholder="{{ mca_sett('location.neighborhood_placeholder') }}">
                <input type="hidden" name="settings[{{ ContactAddressField::NEIGHBORHOOD_ID_KEY }}]" value="0">
            </div>
        @endif
    </div>
</div>
