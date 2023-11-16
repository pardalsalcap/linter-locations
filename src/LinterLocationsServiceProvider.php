<?php

namespace Pardalsalcap\LinterLocations;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Pardalsalcap\LinterLocations\Commands\LinterLocationsCommand;

class LinterLocationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('linter-locations')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_linter-locations_table')
            ->hasCommand(LinterLocationsCommand::class);
    }
}
