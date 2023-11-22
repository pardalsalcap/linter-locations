<?php

namespace Pardalsalcap\LinterLocations;

use Pardalsalcap\LinterLocations\Commands\LinterLocationsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile('linter-locations')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_linter_locations_tables')
            ->hasCommand(LinterLocationsCommand::class);
    }

    public function register()
    {
        parent::register();
        $this->app->register(EventServiceProvider::class);
    }
}
