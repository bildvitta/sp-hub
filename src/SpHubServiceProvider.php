<?php

namespace BildVitta\SpHub;

use BildVitta\SpHub\Commands\Commands\Messages\HubMessageWorkerCommand;
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
            ]);
    }
}
