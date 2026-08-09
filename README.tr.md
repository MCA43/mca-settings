# mca/settings

**Türkçe** | [English](README.md)

Laravel için uygulama ayarları: DB tabanlı key-value deposu ve root-only yönetim arayüzü.

## Özellikler

- **9 grup** — genel, güvenlik, yerel, marka, iletişim, sosyal, entegrasyon, SEO, bakım
- **İletişim repeater** — telefon, e-posta; il/ilçe/mahalle (address paketi ile select)
- **Kaynak projeler** — ima-cms, laravel-emlak, vibromek ortak alanlar
- **Key-value depolama** — `mca_settings` tablosu, tip desteği
- **Helper API** — `mca_setting()`, `mca_setting_bool()`
- **Admin UI** — `/mca/settings` grup sekmeleri, Aktif/Pasif toggle
- **Bakım modu** — `maintenance.*` + opsiyonel middleware (vibromek modeli)
- **HTTPS zorla** — `security.force_https` (varsayılan kapalı) + açıkken web middleware
- **Hub entegrasyonu** — `/mca` panelinde Ayarlar kartı

## Hariç tutulanlar (ayrı paket)

| Konu | Paket |
|------|-------|
| reCAPTCHA, Turnstile, hCaptcha | `mca/captcha` |
| SMTP / e-posta | `mca/smtp` |
| SMS / NetGSM | `mca/netgsm` |
| Ülke / şehir / ilçe select UI | `mca/address` (kurulu değilse metin input) |
| Lisans | `mca/licence` (planlı) |

## Kurulum

```bash
composer require mca/settings
php artisan mca:settings:install
```

Permission ve hub ile:

```bash
composer require mca/permission mca/hub mca/settings
php artisan mca:permission:install
php artisan mca:settings:install
php artisan vendor:publish --tag=mca-permission-assets --force
```

## Yapılandırma

```env
MCA_SETTINGS_ENABLED=true
MCA_SETTINGS_USE_PERMISSION_ROOT=true
MCA_SETTINGS_MAINTENANCE_MIDDLEWARE=false
MCA_SETTINGS_FORCE_HTTPS_MIDDLEWARE=true
```

Bakım modu middleware'i açmak için `MCA_SETTINGS_MAINTENANCE_MIDDLEWARE=true` — public site 503 döner, `/mca/*` ve auth rotaları açık kalır.

HTTPS zorlama **Güvenlik** sekmesinden (`security.force_https`, varsayılan kapalı) yönetilir. Middleware varsayılan olarak `web` grubuna eklenir (`MCA_SETTINGS_FORCE_HTTPS_MIDDLEWARE`); toggle kapalıyken işlem yapmaz. TLS sonlandırma (proxy/CDN) kullanıyorsanız yönlendirme döngüsünü önlemek için önce Laravel `TrustProxies` ayarlayın.

Yeni tanımlar eklendikten sonra:

```bash
php artisan mca:settings:sync
```

## Kod kullanımı

```php
$siteName = mca_setting('general.site_name', 'MCA App');

foreach (mca_setting_social_links() as $social) {
    // ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'link' => 'https://...']
}

foreach (mca_setting_contact_phones() as $phone) {
    // ['label' => 'Merkez', 'number' => '+90...', 'type' => 'phone']
}

foreach (mca_setting_contact_emails() as $email) {
    // ['label' => 'İletişim', 'email' => 'info@...', 'type' => 'contact']
}

$location = mca_setting_contact_location();
// city, district, neighborhood + city_id, district_id, neighborhood_id

// Harita: embed öncelikli, yoksa koordinat
if (mca_setting_map_mode() === 'embed') {
    echo mca_setting_map_embed_html();
} elseif (mca_setting_map_mode() === 'coordinates') {
    $src = mca_setting_map_osm_embed_url();
    // <iframe src="{{ $src }}" ...>
}

mca_settings()->set('general.site_name', 'Yeni Ad');
```

## Publish

```bash
php artisan vendor:publish --tag=mca-settings-config
php artisan vendor:publish --tag=mca-settings-assets --force
```

## GitHub

Depo: [github.com/MCA43/mca-settings](https://github.com/MCA43/mca-settings)

## Lisans

[MIT](LICENSE)
