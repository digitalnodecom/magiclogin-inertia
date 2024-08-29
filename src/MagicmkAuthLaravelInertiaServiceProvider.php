<?php

namespace DigitalNode\MagicmkAuthLaravelInertia;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DigitalNode\MagicmkAuthLaravelInertia\Commands\MagicmkAuthLaravelInertiaCommand;

class MagicmkAuthLaravelInertiaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('magicmk-auth-laravel-inertia')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_magicmk_auth_laravel_inertia_table')
            ->hasCommand(MagicmkAuthLaravelInertiaCommand::class);
    }
}
