<?php

namespace Mca\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mca\Settings\Services\SettingsService;
use Mca\Settings\Support\ContactAddressField;
use Mca\Settings\Support\ContactEmailsField;
use Mca\Settings\Support\ContactMapField;
use Mca\Settings\Support\ContactPhonesField;
use Mca\Settings\Support\ImageUploadField;
use Mca\Settings\Support\PermissionRoleField;
use Mca\Settings\Support\SocialLinksField;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $group = (string) $this->input('group', 'general');
        $items = app(SettingsService::class)->forGroup($group);

        $rules = [
            'group' => ['required', 'string'],
            'settings' => ['required', 'array'],
        ];

        foreach ($items as $item) {
            $key = (string) ($item['key'] ?? '');
            if ($key === '') {
                continue;
            }

            $ruleKey = $this->settingsRuleKey($key);
            $type = (string) ($item['type'] ?? 'string');
            $widget = (string) ($item['widget'] ?? '');

            if ($key === 'security.registration_default_role' || $widget === 'permission_role') {
                $rules[$ruleKey] = $this->roleFieldRules();

                continue;
            }

            if ($widget === 'social_links') {
                $rules[$ruleKey] = ['nullable', 'array'];
                $rules[$ruleKey.'.*.icon'] = ['nullable', 'string', 'max:120'];
                $rules[$ruleKey.'.*.name'] = ['nullable', 'string', 'max:120'];
                $rules[$ruleKey.'.*.link'] = ['nullable', 'string', 'max:500'];

                continue;
            }

            if ($widget === 'contact_emails') {
                $rules[$ruleKey] = ['nullable', 'array'];
                $rules[$ruleKey.'.*.label'] = ['nullable', 'string', 'max:120'];
                $rules[$ruleKey.'.*.email'] = ['nullable', 'email', 'max:190'];
                $rules[$ruleKey.'.*.type'] = ['nullable', 'string', 'in:'.implode(',', array_keys(ContactEmailsField::types()))];

                continue;
            }

            if ($key === ContactAddressField::CITY_ID_KEY || $key === ContactAddressField::DISTRICT_ID_KEY || $key === ContactAddressField::NEIGHBORHOOD_ID_KEY) {
                $rules[$ruleKey] = ['nullable', 'integer', 'min:0'];

                continue;
            }

            if ($widget === 'contact_phones') {
                $rules[$ruleKey] = ['nullable', 'array'];
                $rules[$ruleKey.'.*.label'] = ['nullable', 'string', 'max:120'];
                $rules[$ruleKey.'.*.number'] = ['nullable', 'string', 'max:40'];
                $rules[$ruleKey.'.*.type'] = ['nullable', 'string', 'in:'.implode(',', array_keys(ContactPhonesField::types()))];

                continue;
            }

            if ($key === ContactMapField::LAT_KEY) {
                $rules[$ruleKey] = ['nullable', 'numeric', 'between:-90,90'];

                continue;
            }

            if ($key === ContactMapField::LNG_KEY) {
                $rules[$ruleKey] = ['nullable', 'numeric', 'between:-180,180'];

                continue;
            }

            if ($widget === 'image_upload') {
                $rules[$ruleKey] = ImageUploadField::pathRules();
                $rules['uploads.'.str_replace('.', '\.', $key)] = ImageUploadField::fileRules();

                continue;
            }

            $rules[$ruleKey] = match ($type) {
                'boolean' => ['nullable', 'in:0,1'],
                'integer' => ['nullable', 'integer'],
                'text' => ['nullable', 'string', 'max:5000'],
                'json' => ['nullable', 'array'],
                default => ['nullable', 'string', 'max:500'],
            };
        }

        return $rules;
    }

    /** Flat settings keys may contain dots — escape for Laravel validator. */
    private function settingsRuleKey(string $key): string
    {
        return 'settings.'.str_replace('.', '\.', $key);
    }

    /** @return array<string, mixed> */
    public function validatedSettings(): array
    {
        $settings = $this->input('settings');

        return is_array($settings) ? $settings : [];
    }

    /** @return list<string|\Illuminate\Contracts\Validation\ValidationRule> */
    private function roleFieldRules(): array
    {
        if (PermissionRoleField::isAvailable()) {
            $table = (new \Mca\Permission\Models\Role)->getTable();

            return ['nullable', 'string', Rule::exists($table, 'id')];
        }

        return ['nullable', 'string', 'max:64'];
    }
}
