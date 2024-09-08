<?php

namespace DigitalNode\MagicmkAuthLaravelInertia\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMagicAuth extends Command
{
    protected $signature = 'magicmk:install';
    protected $description = 'Install the MagicmkAuthLaravelInertia package';

    public function handle(): void
    {
        $this->installMigration();
        $this->installMagicAuthController();
        $this->installWebRoutes();
        $this->installVuePage();
        $this->installIntegrationScript();
        $this->info('MagicmkAuthLaravelInertia installed successfully.');
    }

    protected function installWebRoutes(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $routeStubPath = __DIR__ . '/../stubs/web.stub';

        $routeToAdd = File::get($routeStubPath);
        File::append($webRoutesPath, "\n" . $routeToAdd);
        $this->info('Magic login route added to web.php.');
    }

    protected function installMigration(): void
    {
        $newMigrationName = 'database/migrations/'. date('Y_m_d_His') .'_make_name_and_password_nullable_in_users_table.php';
        File::copy(__DIR__ . '/../../database/migrations/make_name_and_password_nullable_in_users_table.php', $newMigrationName);
        $this->info('create_users_table migration overwritten.');
    }


    protected function installMagicAuthController(): void
    {
        $controllerPath = app_path('Http/Controllers/MagicAuthController.php');
        File::copy(__DIR__ . '/../stubs/MagicAuthController.stub', $controllerPath);
        $this->info('MagicAuthController installed.');
    }

    protected function installVuePage(): void
    {
        $vuePath = resource_path('js/Pages/Auth/MagicAuth.vue');
        File::copy(__DIR__ . '/../../resources/js/Pages/Auth/MagicAuth.vue', $vuePath);
        $this->info('MagicAuth Vue page installed.');
    }

    protected function installIntegrationScript(): void
    {
        $sourcePath = __DIR__ . '/../../resources/js/magicmk_integration.js';
        $destinationPath = resource_path('js/magicmk_integration.js');

        File::copy($sourcePath, $destinationPath);
        $this->info('magicmk_integration.js script installed.');
    }
}

