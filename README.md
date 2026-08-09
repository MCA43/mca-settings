# mca/settings

**English** | [Türkçe](README.tr.md)

Application settings for Laravel: database key-value store with root-only admin UI.

## Features

- **9 groups** — general, security, locale, branding, contact, social, integrations, SEO, maintenance
- **Contact repeaters** — phones, emails; city/district/neighborhood (select when `mca/address` is installed)
- **Source projects** — shared fields from ima-cms, laravel-emlak, vibromek
- **Key-value storage** — `mca_settings` table with typed values
- **Helper API** — `mca_setting()`, `mca_setting_bool()`
- **Admin UI** — `/mca/settings` group tabs, Active/Inactive toggles
- **Maintenance mode** — `maintenance.*` + optional middleware (vibromek model)
- **Force HTTPS** — `security.force_https` (default off) + web middleware when enabled
- **Hub integration** — Settings card on `/mca` dashboard

## Excluded (separate packages)

| Topic | Package |
|-------|---------|
| reCAPTCHA, Turnstile, hCaptcha | `mca/captcha` |
| SMTP / mail | `mca/smtp` |
| SMS / NetGSM | `mca/netgsm` |
| Country / city / district select UI | `mca/address` (text inputs when not installed) |
| Licence | `mca/licence` (planned) |

## Install

```bash
composer require mca/settings
php artisan mca:settings:install
```

With permission and hub:

```bash
composer require mca/permission mca/hub mca/settings
php artisan mca:permission:install
php artisan mca:settings:install
php artisan vendor:publish --tag=mca-permission-assets --force
```

## Configuration

```env
MCA_SETTINGS_ENABLED=true
MCA_SETTINGS_USE_PERMISSION_ROOT=true
MCA_SETTINGS_MAINTENANCE_MIDDLEWARE=false
MCA_SETTINGS_FORCE_HTTPS_MIDDLEWARE=true
```

Set `MCA_SETTINGS_MAINTENANCE_MIDDLEWARE=true` to return 503 on the public site while `/mca/*` and auth routes stay open.

Force HTTPS is controlled in **Security** (`security.force_https`, default off). The middleware is on the `web` stack by default (`MCA_SETTINGS_FORCE_HTTPS_MIDDLEWARE`); when the toggle is off it is a no-op. Behind TLS termination, configure Laravel `TrustProxies` first to avoid redirect loops.

After adding new definitions:

```bash
php artisan mca:settings:sync
```

## Usage

```php
$siteName = mca_setting('general.site_name', 'MCA App');

foreach (mca_setting_social_links() as $social) {
    // ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'link' => 'https://...']
}

foreach (mca_setting_contact_phones() as $phone) {
    // ['label' => 'HQ', 'number' => '+90...', 'type' => 'phone']
}

foreach (mca_setting_contact_emails() as $email) {
    // ['label' => 'Contact', 'email' => 'info@...', 'type' => 'contact']
}

$location = mca_setting_contact_location();
// city, district, neighborhood + city_id, district_id, neighborhood_id

// Map: embed first, then coordinates
if (mca_setting_map_mode() === 'embed') {
    echo mca_setting_map_embed_html();
} elseif (mca_setting_map_mode() === 'coordinates') {
    $src = mca_setting_map_osm_embed_url();
    // <iframe src="{{ $src }}" ...>
}

mca_settings()->set('general.site_name', 'New Name');
```

## Publish

```bash
php artisan vendor:publish --tag=mca-settings-config
php artisan vendor:publish --tag=mca-settings-assets --force
```

## GitHub

Repository: [github.com/MCA43/mca-settings](https://github.com/MCA43/mca-settings)

## License

[MIT](LICENSE)
