<?php

namespace Mca\Settings\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Mca\Settings\Http\Requests\UpdateSettingsRequest;
use Mca\Settings\Services\SettingsService;
use Mca\Settings\Support\ContactEmailsField;
use Mca\Settings\Support\ContactPhonesField;
use Mca\Settings\Support\McaSettingsView;
use Mca\Settings\Support\SocialLinksField;

class SettingsController
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function index()
    {
        $groups = $this->settings->groups();
        $activeGroup = request('group', $groups[0] ?? 'general');

        if (! in_array($activeGroup, $groups, true)) {
            $activeGroup = $groups[0] ?? 'general';
        }

        $items = $this->settings->forGroup($activeGroup);

        return McaSettingsView::render('settings.index', [
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'items' => $items,
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $group = (string) $request->validated('group');
        $values = $request->validatedSettings();

        $allowedKeys = collect($this->settings->forGroup($group))->pluck('key')->all();
        $filtered = array_intersect_key($values, array_flip($allowedKeys));

        if (array_key_exists(SocialLinksField::KEY, $filtered)) {
            $filtered[SocialLinksField::KEY] = SocialLinksField::normalize($filtered[SocialLinksField::KEY]);
        }

        if (array_key_exists(ContactPhonesField::KEY, $filtered)) {
            $filtered[ContactPhonesField::KEY] = ContactPhonesField::normalize($filtered[ContactPhonesField::KEY]);
        }

        if (array_key_exists(ContactEmailsField::KEY, $filtered)) {
            $filtered[ContactEmailsField::KEY] = ContactEmailsField::normalize($filtered[ContactEmailsField::KEY]);
        }

        $this->settings->updateMany($filtered);

        return redirect()
            ->route('mca.settings.index', ['group' => $group])
            ->with('mca_sett_status', mca_sett('flash.saved'));
    }
}
