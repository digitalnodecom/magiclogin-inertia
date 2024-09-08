<?php

namespace DigitalNode\MagicloginInertia;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MagicLoginInertiaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('magiclogin-inertia')
            ->hasConfigFile()
            ->hasCommand(Console\InstallMagicAuth::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/inertia.php', 'inertia'
        );

    }
}
