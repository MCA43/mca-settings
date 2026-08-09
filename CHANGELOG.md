# Changelog

## [0.3.1] - 2026-07-26

### Changed
- Definition `label` / `description` from host config override DB metadata in the admin UI
- `image_upload` fields use compact grid tiles (not full-width text rows)

## [0.3.0] - 2026-07-26

### Added
- `image_upload` widget (soft-dep on `mca/uploads`): file picker + preview for branding paths
- Multipart settings form (`enctype="multipart/form-data"`)
- `ImageUploadField` — validates `uploads[setting.key]`, applies `mca_upload()->replace()` on save
- EN/TR hints for image upload and text-path fallback when uploads is missing

### Notes
- Host / package definitions ship with `'widget' => 'image_upload'` on branding keys (`branding.favicon`, logos)
- Preset name = setting key (must match `config/upload.php` presets)

## [0.2.0] - 2026-06-28

### Added
- 9 setting groups: general, security, locale, branding, contact, social, integrations, SEO, maintenance
- Repeater widgets: `social.links`, `contact.phones`, `contact.emails`
- Contact address widget: text inputs or cascading selects when `mca/address` is installed
- Contact map widget: embed HTML + latitude/longitude (embed takes priority)
- Helpers: `mca_setting_social_links()`, `mca_setting_contact_phones()`, `mca_setting_contact_emails()`, `mca_setting_contact_location()`, `mca_setting_map_*()`
- `mca:settings:sync` — sync definitions, migrate legacy keys, prune orphaned records
- Maintenance mode middleware (`mca.settings.maintenance`, optional web stack)
- Boolean toggle UI, permission role field when `mca/permission` is installed

### Changed
- Mail and SMS groups removed (`mca/smtp`, `mca/netgsm` are separate packages)
- Social media: per-platform fields replaced by JSON repeater (`social.links`)
- Technical key display off by default (`MCA_SETTINGS_UI_SHOW_KEYS`)

### Removed
- `mail.*`, `sms.*` groups
- Legacy contact fields (`contact.phone`, `contact.email`, `contact.system_email`, etc.)
- `general.tc_verification`, `seo.meta_keywords`

### Notes
- reCAPTCHA / Turnstile → `mca/captcha` (planned)
- Sitemap / robots.txt generation → application or future `mca/seo` package; settings holds SEO defaults only

## [0.1.0] - 2026-06-28

### Added
- Database key-value store (`mca_settings`)
- `mca_setting()` / `mca_settings()` helpers
- Root-only admin UI (`/mca/settings`)
- `mca:settings:install` command
- Hub integration (`extra.mca`)
- English and Turkish translations
