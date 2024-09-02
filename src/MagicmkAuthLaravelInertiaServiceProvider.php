<?php

namespace DigitalNode\MagicmkAuthLaravelInertia;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MagicmkAuthLaravelInertiaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('magicmk-auth-laravel-inertia')
            ->hasConfigFile()
            ->hasCommand(Console\InstallMagicAuth::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/inertia.php', 'inertia'
        );

    }
}
