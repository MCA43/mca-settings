<?php

namespace Mca\Settings\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Mca\Permission\Services\PackageAccessService;
use Mca\Settings\Http\Requests\UpdateSettingsRequest;
use Mca\Settings\Services\SettingsService;
use Mca\Settings\Support\ContactEmailsField;
use Mca\Settings\Support\ContactPhonesField;
use Mca\Settings\Support\DatetimeField;
use Mca\Settings\Support\ImageUploadField;
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
        $user = request()->user();

        if (class_exists(PackageAccessService::class)) {
            $groups = app(PackageAccessService::class)->filterSettingsGroups($user, $groups);
        }

        $activeGroup = (string) request('group', $groups[0] ?? 'general');

        if ($groups === []) {
            return McaSettingsView::render('settings.index', [
                'groups' => [],
                'activeGroup' => '',
                'items' => [],
            ]);
        }

        if (request()->filled('group') && ! in_array($activeGroup, $groups, true)) {
            abort(403, mca_sett('errors.group_forbidden'));
        }

        if (! in_array($activeGroup, $groups, true)) {
            $activeGroup = $groups[0];
        }

        if (class_exists(PackageAccessService::class)
            && ! app(PackageAccessService::class)->allowsSettingsGroup($user, $activeGroup)) {
            abort(403, mca_sett('errors.group_forbidden'));
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
        $user = $request->user();

        if (class_exists(PackageAccessService::class)
            && ! app(PackageAccessService::class)->allowsSettingsGroup($user, $group)) {
            abort(403, mca_sett('errors.group_forbidden'));
        }

        $values = $request->validatedSettings();

        $groupItems = $this->settings->forGroup($group);
        $allowedKeys = collect($groupItems)->pluck('key')->all();
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

        $filtered = DatetimeField::normalizeGroup($filtered, $groupItems);
        $filtered = ImageUploadField::applyUploads($request, $filtered, $groupItems);

        foreach ($filtered as $key => $value) {
            $definition = $this->settings->definitionFor((string) $key) ?? [];
            if (($definition['widget'] ?? '') !== 'password') {
                continue;
            }

            $trimmed = is_string($value) ? trim($value) : '';
            if ($trimmed === '' || $trimmed === '••••••••') {
                unset($filtered[$key]);
            }
        }

        $this->settings->updateMany($filtered);

        return redirect()
            ->route('mca.settings.index', ['group' => $group])
            ->with('mca_sett_status', mca_sett('flash.saved'));
    }
}
