<?php

namespace DigitalNode\MagicloginInertia\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallMagicAuth extends Command
{
    protected $signature = 'magiclogin:install';
    protected $description = 'Install the MagicmkAuthLaravelInertia package';

    public function handle(): void
    {
        $this->warn("Will the authentication be via LINK or CODE?");
        $linkSelected = $this->confirm('"yes"-LINK ; "no"-CODE');
        $this->installMigration();
        $this->installMagicAuthController();
        $this->installWebRoutes();
        $this->installVuePage();
        $this->installIntegrationScript($linkSelected);
        $this->publishCustomUpdateUserPasswordAction();
        $this->configureFortifyToUseCustomAction();
        $this->updateEnvFile();
        $this->info('MagicmkAuthLaravelInertia installed successfully.');
        $this->error('Remember to add the project id (slug) and project api key from your magic mk project to your .env');
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
        $newMigrationName = 'database/migrations/' . date('Y_m_d_His') . '_make_name_and_password_nullable_in_users_table.php';
        File::copy(__DIR__ . '/../../database/migrations/make_name_and_password_nullable_in_users_table.php', $newMigrationName);
        $this->info('make_name_and_password_nullable_in_users_table migration installed.');
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

    protected function installIntegrationScript($linkSelected): void
    {
        if ($linkSelected) {
            $sourcePath = __DIR__ . '/../../resources/js/link-integration/magicmk_integration.js';
        } else {
            $sourcePath = __DIR__ . '/../../resources/js/code-integration/magicmk_integration.js';
        }

        $destinationPath = resource_path('js/magicmk_integration.js');

        File::copy($sourcePath, $destinationPath);
        $this->info('magicmk_integration.js script installed.');
    }

    protected function updateEnvFile(): void
    {
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            File::append($envPath, "\nMAGIC_LOGIN_PROJECT_KEY=\"\"\nMAGIC_LOGIN_API_KEY=\"\"\n");
            $this->info('.env file updated with MAGIC_LOGIN_PROJECT_KEY and MAGIC_LOGIN_API_KEY.');
        } else {
            $this->error('.env file not found.');
        }
    }

    protected function publishCustomUpdateUserPasswordAction(): void
    {
        $sourcePath = __DIR__ . '/../stubs/MagicLoginUpdateUserPassword.stub';
        $destinationPath = app_path('Actions/Fortify/MagicLoginUpdateUserPassword.php');

        if (!File::exists($sourcePath)) {
            $this->error("Stub file not found at $sourcePath");
            return;
        }

        $destinationDir = dirname($destinationPath);
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
            $this->info("Created directory $destinationDir");
        }

        if (File::exists($destinationPath)) {
            $this->warn('Custom MagicLoginUpdateUserPassword action already exists.');
            return;
        }

        File::copy($sourcePath, $destinationPath);

        $this->info('Custom MagicLoginUpdateUserPassword action published.');
    }

    protected function configureFortifyToUseCustomAction(): void
    {
        $providerPath = app_path('Providers/FortifyServiceProvider.php');

        if (!File::exists($providerPath)) {
            $this->error('FortifyServiceProvider.php not found.');
            return;
        }

        $fileContents = File::get($providerPath);

        $replacementBinding = 'Fortify::updateUserPasswordsUsing(\\App\\Actions\\Fortify\\MagicLoginUpdateUserPassword::class);';

        $pattern = '/Fortify::updateUserPasswordsUsing\([^)]+\);/';

        if (Str::contains($fileContents, $replacementBinding)) {
            $this->info('FortifyServiceProvider.php already configured to use MagicLoginUpdateUserPassword.');
            return;
        }

        if (preg_match($pattern, $fileContents)) {
            $fileContents = preg_replace($pattern, $replacementBinding, $fileContents, 1, $count);

            if ($count > 0) {
                File::put($providerPath, $fileContents);
                $this->info('Updated Fortify::updateUserPasswordsUsing binding in FortifyServiceProvider.php.');
            } else {
                $this->error('Failed to update Fortify::updateUserPasswordsUsing binding.');
                return;
            }
        } else {
            $this->error('Could not find existing Fortify::updateUserPasswordsUsing binding to replace.');
            return;
        }

        $this->info('Fortify configured to use custom UpdateUserPassword action.');
    }

}

