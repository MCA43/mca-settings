<?php

namespace Mca\Settings\Console;

use Illuminate\Console\Command;
use Mca\Settings\Services\SettingsService;
use Mca\Settings\Support\McaSettingsLocale;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mca:settings:sync')]
class SyncSettingsCommand extends Command
{
    protected $signature = 'mca:settings:sync';

    protected $description = 'Sync new setting definitions from config into the database';

    public function handle(SettingsService $settings): int
    {
        McaSettingsLocale::apply();

        $created = $settings->syncDefinitions();
        $migrated = $settings->migrateSocialLinksFromLegacy();
        $migratedPhones = $settings->migrateContactPhonesFromLegacy();
        $migratedEmails = $settings->migrateContactEmailsFromLegacy();
        $upgraded = $settings->upgradeSocialLinksFormat();
        $pruned = $settings->pruneOrphaned();

        $this->components->info(mca_sett('console.sync.done', ['count' => $created]));

        if ($migrated) {
            $this->components->info(mca_sett('console.sync.migrated_social'));
        }

        if ($migratedPhones) {
            $this->components->info(mca_sett('console.sync.migrated_phones'));
        }

        if ($migratedEmails) {
            $this->components->info(mca_sett('console.sync.migrated_emails'));
        }

        if ($pruned > 0) {
            $this->components->info(mca_sett('console.sync.pruned', ['count' => $pruned]));
        }

        return self::SUCCESS;
    }
}
