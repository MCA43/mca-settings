@extends(\Mca\Settings\Support\McaSettingsView::layout())

@php
    $groupLabel = mca_sett_group($activeGroup);
    $showKeys = (bool) config('settings.ui.show_keys', false);
@endphp

@section('title', $groupLabel.' — '.($mcaSettTitle ?? mca_sett('app.title')))

@section('content')
    <div class="mca-perm-toolbar">
        <div>
            <h1 class="mca-perm-toolbar__title">{{ $groupLabel }}</h1>
            <p class="mca-perm-toolbar__subtitle">{{ mca_sett('page.subtitle') }}</p>
        </div>
    </div>

    <div class="mca-perm-layout-split mca-sett-layout">
        <aside class="mca-perm-card mca-sett-sidebar" aria-label="{{ mca_sett('nav.groups') }}">
            <div class="mca-perm-card__header">{{ mca_sett('nav.groups') }}</div>
            <nav class="mca-sett-sidebar__nav">
                @foreach ($groups as $group)
                    <a href="{{ route('mca.settings.index', ['group' => $group]) }}"
                       class="mca-sett-sidebar__link {{ $activeGroup === $group ? 'mca-sett-sidebar__link--active' : '' }}">
                        {{ mca_sett_group($group) }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="mca-perm-card mca-perm-card__body mca-sett-form-card">
            @if ($items === [])
                <p class="mca-perm-help mca-sett-empty">{{ mca_sett('empty') }}</p>
            @else
                <form method="post"
                      action="{{ route('mca.settings.update') }}"
                      class="mca-sett-form"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="group" value="{{ $activeGroup }}">

                    <div class="mca-sett-fields">
                        @foreach ($items as $item)
                            @php
                                $key = $item['key'];
                                $definition = app(\Mca\Settings\Services\SettingsService::class)->definitionFor($key) ?? [];
                                if (($definition['form'] ?? true) === false) {
                                    continue;
                                }
                                $type = $item['type'] ?? 'string';
                                $widget = $item['widget'] ?? ($definition['widget'] ?? '');
                                $value = app(\Mca\Settings\Services\SettingsService::class)->get($key);
                                $label = \Mca\Settings\Support\McaSettingsView::label($item['label'] ?? $key);
                                $description = \Mca\Settings\Support\McaSettingsView::label($item['description'] ?? '');
                                $wide = in_array($type, ['text', 'boolean', 'json'], true)
                                    || ($widget !== '' && $widget !== 'image_upload');
                                $uploadField = $widget === 'image_upload';
                                $options = $item['options'] ?? ($definition['options'] ?? []);
                                $passwordSet = $widget === 'password' && is_string($value) && trim($value) !== '';
                            @endphp

                            <div class="mca-perm-field mca-sett-field {{ $wide ? 'mca-sett-field--wide' : '' }} {{ $uploadField ? 'mca-sett-field--upload' : '' }}">
                                @if (! in_array($widget, ['social_links', 'contact_phones', 'contact_emails', 'contact_address', 'contact_map'], true))
                                    <label class="mca-perm-label" for="sett-{{ md5($key) }}">
                                        {{ $label }}
                                        @if ($showKeys)
                                            <code class="mca-perm-mono mca-sett-field__key">{{ $key }}</code>
                                        @endif
                                    </label>

                                    @if ($description !== '' && $widget !== 'image_upload')
                                        <p class="mca-perm-help">{{ $description }}</p>
                                    @endif
                                @endif

                                @if ($widget === 'social_links')
                                    @include('mca-settings::partials.fields.social-links', [
                                        'links' => \Mca\Settings\Support\SocialLinksField::valueForForm(app(\Mca\Settings\Services\SettingsService::class)),
                                    ])
                                @elseif ($widget === 'contact_phones')
                                    @include('mca-settings::partials.fields.contact-phones', [
                                        'phones' => \Mca\Settings\Support\ContactPhonesField::valueForForm(app(\Mca\Settings\Services\SettingsService::class)),
                                    ])
                                @elseif ($widget === 'contact_emails')
                                    @include('mca-settings::partials.fields.contact-emails', [
                                        'emails' => \Mca\Settings\Support\ContactEmailsField::valueForForm(app(\Mca\Settings\Services\SettingsService::class)),
                                    ])
                                @elseif ($widget === 'contact_address')
                                    @include('mca-settings::partials.fields.contact-address')
                                @elseif ($widget === 'contact_map')
                                    @include('mca-settings::partials.fields.contact-map')
                                @elseif ($widget === 'permission_role')
                                    @include('mca-settings::partials.fields.permission-role', [
                                        'key' => $key,
                                        'value' => $value,
                                    ])
                                @elseif ($widget === 'image_upload')
                                    @include('mca-settings::partials.fields.image-upload', [
                                        'key' => $key,
                                        'value' => $value,
                                    ])
                                @elseif ($widget === 'datetime')
                                    <input type="datetime-local"
                                           id="sett-{{ md5($key) }}"
                                           name="settings[{{ $key }}]"
                                           class="mca-perm-input mca-sett-datetime"
                                           value="{{ \Mca\Settings\Support\DatetimeField::toInputValue(old('settings.'.$key, $value)) }}">
                                @elseif ($type === 'boolean')
                                    <div class="mca-ui-toggle" role="radiogroup" aria-label="{{ $label }}">
                                        <label class="mca-ui-toggle__opt">
                                            <input type="radio"
                                                   name="settings[{{ $key }}]"
                                                   value="0"
                                                   @checked(! (bool) $value)>
                                            <span>{{ mca_sett('form.passive') }}</span>
                                        </label>
                                        <label class="mca-ui-toggle__opt mca-ui-toggle__opt--on">
                                            <input type="radio"
                                                   name="settings[{{ $key }}]"
                                                   value="1"
                                                   @checked((bool) $value)>
                                            <span>{{ mca_sett('form.active') }}</span>
                                        </label>
                                    </div>
                                @elseif ($type === 'text')
                                    <textarea id="sett-{{ md5($key) }}"
                                              name="settings[{{ $key }}]"
                                              class="mca-perm-input mca-sett-textarea"
                                              rows="4">{{ old('settings.'.$key, $value) }}</textarea>
                                @elseif ($widget === 'select' && is_array($options) && $options !== [])
                                    <select id="sett-{{ md5($key) }}"
                                            name="settings[{{ $key }}]"
                                            class="mca-perm-input">
                                        @foreach ($options as $optionValue => $optionLabel)
                                            @php
                                                $optLabel = is_array($optionLabel)
                                                    ? \Mca\Settings\Support\McaSettingsView::label($optionLabel)
                                                    : (string) $optionLabel;
                                            @endphp
                                            <option value="{{ $optionValue }}"
                                                @selected((string) old('settings.'.$key, $value) === (string) $optionValue)>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif ($widget === 'password')
                                    <input type="password"
                                           id="sett-{{ md5($key) }}"
                                           name="settings[{{ $key }}]"
                                           class="mca-perm-input"
                                           autocomplete="new-password"
                                           value=""
                                           placeholder="{{ $passwordSet ? '••••••••' : '' }}">
                                    @if ($passwordSet)
                                        <p class="mca-perm-help">{{ mca_sett('form.password_keep') }}</p>
                                    @endif
                                @else
                                    <input type="{{ $type === 'integer' ? 'number' : 'text' }}"
                                           id="sett-{{ md5($key) }}"
                                           name="settings[{{ $key }}]"
                                           class="mca-perm-input"
                                           value="{{ old('settings.'.$key, $value) }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mca-perm-form-actions mca-sett-actions">
                        <button type="submit" class="mca-perm-btn mca-perm-btn--primary">
                            @include('mca-settings::partials.icon', ['name' => 'save', 'class' => 'mca-ui-icon mca-ui-icon--sm'])
                            {{ mca_sett('form.save') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
