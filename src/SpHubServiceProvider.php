<?php

namespace BildVitta\SpHub;

use BildVitta\SpHub\Console\Commands\DataImport\Hub\HubImportCommand;
use BildVitta\SpHub\Console\Commands\Messages\HubMessageWorkerCommand;
use BildVitta\SpHub\Console\Commands\InstallSp;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpHubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('sp-hub')
            ->hasConfigFile()
            ->hasCommands([
                HubMessageWorkerCommand::class,
                InstallSp::class,
                HubImportCommand::class,
            ])
            ->hasMigrations([
                'add_column_main_company_id_on_hub_companies',
            ])
            ->runsMigrations();
    }
}
