<?php

namespace DigitalNode\MagicmkAuthLaravelInertia\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMagicAuth extends Command
{
    protected $signature = 'magicauth:install';
    protected $description = 'Install the MagicmkAuthLaravelInertia package';

    public function handle(): void
    {
        $this->error('Existing User model and migration will be overwritten!');

        if (!$this->confirm("Continue?", true)) {
            $this->info('Installation cancelled.');
            return;
        }

        $this->installMigration();
        $this->installUserModel();
        $this->installMagicAuthController();

        $this->error('Existing /routes/web.php will be overwritten!');

        if (!$this->confirm("Continue?", true)) {
            $this->info('Installation cancelled.');
            return;
        }

        $this->installWebRoutes();

        $this->error('Existing /Auth directory will be overwritten!');

        if (!$this->confirm("Continue?", true)) {
            $this->info('Installation cancelled.');
            return;
        }

        $this->installVuePage();
        $this->installIntegrationScript();
        $this->info('MagicmkAuthLaravelInertia installed successfully.');
    }

    protected function installWebRoutes(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        File::copy(__DIR__ . '/../stubs/web.stub', $webRoutesPath);
        $this->info('web.php routes file has been overwritten.');
    }

    protected function installMigration(): void
    {
        $newMigrationName = 'database/migrations/0001_01_01_000000_create_users_table.php';
        File::copy(__DIR__ . '/../../database/migrations/create_users_table.php', $newMigrationName);
        $this->info('create_users_table migration overwritten.');
    }

    protected function installUserModel(): void
    {
        $userModelPath = app_path('Models/User.php');
        File::copy($userModelPath, __DIR__ . '/../stubs/User.stub');
        $this->info('User model overwritten.');
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
        $authFolderPath = resource_path('js/Pages/Auth');

        $files = File::allFiles($authFolderPath);
        foreach ($files as $file) {
            File::delete($file->getPathname());
        }
        $this->info('Auth folder emptied.');

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

