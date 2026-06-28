<?php

namespace Mca\Settings\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Mca\Settings\Services\SettingsService;
use Mca\Settings\Support\McaSettingsLocale;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mca:settings:install')]
class InstallSettingsCommand extends Command
{
    protected $signature = 'mca:settings:install
                            {--no-assets : Skip CSS publish}
                            {--no-sync : Skip definition sync}';

    protected $description = 'Install MCA Settings (migration, defaults, assets)';

    public function handle(SettingsService $settings): int
    {
        McaSettingsLocale::apply();

        $this->components->info(mca_sett('console.install.start'));

        if (! file_exists(config_path('settings.php'))) {
            $this->callSilent('vendor:publish', ['--tag' => 'mca-settings-config']);
        }
        $this->components->task(mca_sett('console.install.config_ready'), fn () => true);

        if (! $this->option('no-assets')) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'mca-settings-assets',
                '--force' => true,
            ]);
            $this->components->task(mca_sett('console.install.assets_published'), fn () => true);
        }

        Artisan::call('migrate', ['--force' => true]);
        $this->output->write(Artisan::output());
        $this->components->task(mca_sett('console.install.migration_done'), fn () => true);

        if (! $this->option('no-sync')) {
            $created = $settings->syncDefinitions();
            $settings->migrateSocialLinksFromLegacy();
            $settings->migrateContactPhonesFromLegacy();
            $settings->migrateContactEmailsFromLegacy();
            $settings->upgradeSocialLinksFormat();
            $settings->pruneOrphaned();
            $this->components->task(mca_sett('console.install.sync_done', ['count' => $created]), fn () => true);
        }

        $this->newLine();
        $this->components->info(mca_sett('console.install.done'));
        $this->line('  '.mca_sett('console.install.web_ui', [
            'prefix' => config('settings.routes.web.prefix', 'mca/settings'),
        ]));

        return self::SUCCESS;
    }
}
