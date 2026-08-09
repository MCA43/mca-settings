<?php

/**
 * Default MCA settings (ima-cms / laravel-emlak / vibromek ortak alanlar).
 *
 * Hariç: reCAPTCHA, Turnstile, hCaptcha → ayrı paketler (mca/captcha-*)
 * Hariç: licence → ayrı paket
 * country_id / city_id / district_id → mca/address paketi ile select UI (şimdilik integer)
 */

$label = static fn (string $en, string $tr): array => ['en' => $en, 'tr' => $tr];

return [

    'general' => [
        'general.site_name' => [
            'type' => 'string',
            'default' => 'MCA App',
            'label' => $label('Site name', 'Site adı'),
            'description' => $label('Browser title, emails, branding.', 'Tarayıcı başlığı ve e-postalarda görünür.'),
            'sort' => 10,
        ],
        'general.site_slogan' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Site slogan', 'Site sloganı'),
            'sort' => 20,
        ],
        'general.demo_mode' => [
            'type' => 'boolean',
            'default' => false,
            'label' => $label('Demo mode', 'Demo modu'),
            'description' => $label('Blocks panel mutations except root.', 'Root dışında panel yazma işlemlerini engeller.'),
            'sort' => 30,
        ],
        'general.registering' => [
            'type' => 'boolean',
            'default' => false,
            'label' => $label('External registration', 'Sisteme dış kayıt'),
            'description' => $label('Allow public user registration.', 'Harici kullanıcı kaydına izin ver.'),
            'sort' => 40,
        ],
    ],

    'security' => [
        'security.password_reset_enabled' => [
            'type' => 'boolean',
            'default' => true,
            'label' => $label('Password reset', 'Şifre sıfırlama'),
            'sort' => 10,
        ],
        'security.registration_requires_approval' => [
            'type' => 'boolean',
            'default' => false,
            'label' => $label('Registration requires approval', 'Kayıt onay gerektirir'),
            'sort' => 20,
        ],
        'security.registration_default_role' => [
            'type' => 'string',
            'widget' => 'permission_role',
            'default' => 'editor',
            'label' => $label('Default role for new users', 'Yeni kullanıcı varsayılan rolü'),
            'description' => $label(
                'Role assigned on registration (role id or slug).',
                'Kayıtta atanacak rol (role id veya slug).'
            ),
            'sort' => 30,
        ],
        'security.registration_allowed_domains' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Allowed email domains (comma separated)', 'İzinli e-posta domainleri (virgülle)'),
            'description' => $label('Empty = all domains.', 'Boş = tüm domainler.'),
            'sort' => 40,
        ],
        'security.force_https' => [
            'type' => 'boolean',
            'default' => false,
            'label' => $label('Force HTTPS', 'HTTPS zorla'),
            'description' => $label(
                'Redirect HTTP to HTTPS and force https:// in generated URLs. Behind a proxy, configure TrustProxies first.',
                'HTTP isteklerini HTTPS’e yönlendirir ve üretilen URL’lerde https:// zorlar. Proxy arkasında önce TrustProxies ayarlayın.'
            ),
            'sort' => 50,
        ],
    ],

    'locale' => [
        'locale.default_locale' => [
            'type' => 'string',
            'default' => 'tr',
            'label' => $label('Default locale', 'Varsayılan dil'),
            'sort' => 10,
        ],
        'locale.timezone' => [
            'type' => 'string',
            'default' => 'Europe/Istanbul',
            'label' => $label('Timezone', 'Saat dilimi'),
            'sort' => 20,
        ],
        'locale.date_format' => [
            'type' => 'string',
            'default' => 'd.m.Y',
            'label' => $label('Date format', 'Tarih formatı'),
            'sort' => 30,
        ],
    ],

    'branding' => [
        'branding.favicon' => [
            'type' => 'string',
            'widget' => 'image_upload',
            'default' => '',
            'label' => $label('Favicon', 'Favicon'),
            'description' => $label(
                'Upload or keep the current favicon.',
                'Yükleyin veya mevcut favicon’u koruyun.'
            ),
            'sort' => 10,
        ],
        'branding.light_logo' => [
            'type' => 'string',
            'widget' => 'image_upload',
            'default' => '',
            'label' => $label('Light logo', 'Açık tema logo'),
            'description' => $label(
                'Upload or keep the current light logo.',
                'Yükleyin veya mevcut açık tema logosunu koruyun.'
            ),
            'sort' => 20,
        ],
        'branding.light_logo_sm' => [
            'type' => 'string',
            'widget' => 'image_upload',
            'default' => '',
            'label' => $label('Light logo (small)', 'Açık tema logo (küçük)'),
            'description' => $label(
                'Upload or keep the current small light logo.',
                'Yükleyin veya mevcut küçük açık tema logosunu koruyun.'
            ),
            'sort' => 30,
        ],
        'branding.dark_logo' => [
            'type' => 'string',
            'widget' => 'image_upload',
            'default' => '',
            'label' => $label('Dark logo', 'Koyu tema logo'),
            'description' => $label(
                'Upload or keep the current dark logo.',
                'Yükleyin veya mevcut koyu tema logosunu koruyun.'
            ),
            'sort' => 40,
        ],
        'branding.dark_logo_sm' => [
            'type' => 'string',
            'widget' => 'image_upload',
            'default' => '',
            'label' => $label('Dark logo (small)', 'Koyu tema logo (küçük)'),
            'description' => $label(
                'Upload or keep the current small dark logo.',
                'Yükleyin veya mevcut küçük koyu tema logosunu koruyun.'
            ),
            'sort' => 50,
        ],
    ],

    'contact' => [
        'contact.phones' => [
            'type' => 'json',
            'widget' => 'contact_phones',
            'default' => [],
            'label' => $label('Phone numbers', 'Telefon numaraları'),
            'description' => $label(
                'Label, number and type per row.',
                'Satır başına etiket, numara ve tür.'
            ),
            'sort' => 10,
        ],
        'contact.emails' => [
            'type' => 'json',
            'widget' => 'contact_emails',
            'default' => [],
            'label' => $label('Email addresses', 'E-posta adresleri'),
            'description' => $label(
                'Label, email and type per row.',
                'Satır başına etiket, e-posta ve tür.'
            ),
            'sort' => 20,
        ],
        'contact.city' => [
            'type' => 'string',
            'widget' => 'contact_address',
            'default' => '',
            'label' => $label('City / district / neighborhood', 'İl / ilçe / mahalle'),
            'sort' => 90,
        ],
        'contact.district' => [
            'type' => 'string',
            'default' => '',
            'form' => false,
            'label' => $label('District', 'İlçe'),
            'sort' => 91,
        ],
        'contact.neighborhood' => [
            'type' => 'string',
            'default' => '',
            'form' => false,
            'label' => $label('Neighborhood', 'Mahalle'),
            'sort' => 92,
        ],
        'contact.city_id' => [
            'type' => 'integer',
            'default' => 0,
            'form' => false,
            'label' => $label('City ID', 'İl ID'),
            'sort' => 93,
        ],
        'contact.district_id' => [
            'type' => 'integer',
            'default' => 0,
            'form' => false,
            'label' => $label('District ID', 'İlçe ID'),
            'sort' => 94,
        ],
        'contact.neighborhood_id' => [
            'type' => 'integer',
            'default' => 0,
            'form' => false,
            'label' => $label('Neighborhood ID', 'Mahalle ID'),
            'sort' => 95,
        ],
        'contact.address' => [
            'type' => 'text',
            'default' => '',
            'label' => $label('Address', 'Adres'),
            'sort' => 100,
        ],
        'contact.map_embed' => [
            'type' => 'text',
            'widget' => 'contact_map',
            'default' => '',
            'label' => $label('Map', 'Harita'),
            'description' => $label(
                'Google Maps iframe or coordinates (embed takes priority).',
                'Google Maps iframe veya koordinat (embed varsa öncelikli).'
            ),
            'sort' => 110,
        ],
        'contact.map_lat' => [
            'type' => 'string',
            'default' => '',
            'form' => false,
            'label' => $label('Map latitude', 'Harita enlem'),
            'sort' => 111,
        ],
        'contact.map_lng' => [
            'type' => 'string',
            'default' => '',
            'form' => false,
            'label' => $label('Map longitude', 'Harita boylam'),
            'sort' => 112,
        ],
    ],

    'social' => [
        'social.links' => [
            'type' => 'json',
            'widget' => 'social_links',
            'default' => [],
            'label' => $label('Social links', 'Sosyal medya bağlantıları'),
            'description' => $label(
                'Icon class, display name and profile URL per row (laravel-emlak substation model).',
                'Satır başına ikon sınıfı, görünen ad ve profil linki (emlak şube modeli).'
            ),
            'sort' => 10,
        ],
    ],

    'integrations' => [
        'integrations.analytics_code' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Analytics ID', 'Analytics kodu'),
            'sort' => 10,
        ],
        'integrations.adsense_code' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('AdSense code', 'AdSense kodu'),
            'sort' => 20,
        ],
        'integrations.head_code' => [
            'type' => 'text',
            'default' => '',
            'label' => $label('Head HTML', '<head> ek kod'),
            'sort' => 30,
        ],
        'integrations.footer_code' => [
            'type' => 'text',
            'default' => '',
            'label' => $label('Footer HTML', 'Footer ek kod'),
            'sort' => 40,
        ],
    ],

    'seo' => [
        'seo.meta_title' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Meta title', 'Meta başlık'),
            'sort' => 10,
        ],
        'seo.meta_description' => [
            'type' => 'text',
            'default' => '',
            'label' => $label('Meta description', 'Meta açıklama'),
            'sort' => 20,
        ],
        'seo.meta_author' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Meta author', 'Meta yazar'),
            'sort' => 40,
        ],
        'seo.robots' => [
            'type' => 'string',
            'default' => 'index, follow',
            'label' => $label('Default robots', 'Varsayılan robots'),
            'sort' => 50,
        ],
    ],

    'maintenance' => [
        'maintenance.enabled' => [
            'type' => 'boolean',
            'default' => false,
            'label' => $label('Maintenance mode', 'Bakım modu'),
            'description' => $label('Public site shows maintenance page; panel/login stay open.', 'Kurumsal sitede bakım sayfası; panel ve giriş açık kalır.'),
            'sort' => 10,
        ],
        'maintenance.title' => [
            'type' => 'string',
            'default' => 'Yakında sizlerleyiz',
            'label' => $label('Maintenance title', 'Bakım başlığı'),
            'sort' => 20,
        ],
        'maintenance.description' => [
            'type' => 'text',
            'default' => '',
            'label' => $label('Maintenance message', 'Bakım mesajı'),
            'sort' => 30,
        ],
        'maintenance.image' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Maintenance image path', 'Bakım görseli yolu'),
            'sort' => 40,
        ],
        'maintenance.end_date' => [
            'type' => 'string',
            'default' => '',
            'label' => $label('Expected end (Y-m-d H:i)', 'Tahmini bitiş (Y-m-d H:i)'),
            'sort' => 50,
        ],
    ],

];
