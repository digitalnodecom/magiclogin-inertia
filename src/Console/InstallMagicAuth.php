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
        $this->installMigration();
        $this->installPhoneMigration();
        $this->installMagicAuthController();
        $this->installWebRoutes();
        $this->installVuePage();
        $this->installIntegrationScript();
        $this->publishCustomUpdateUserPasswordAction();
        $this->configureFortifyToUseCustomAction();
        $this->updateEnvFile();
        $this->info('MagicLoginLaravelInertia installed successfully.');
        $this->error('Remember to add the project id (slug) and project api key from your magic mk project to your .env');
    }

    protected function installWebRoutes(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $routeStubPath = __DIR__ . '/../stubs/web.stub';

        if (!File::exists($routeStubPath)) {
            $this->error("Route stub file not found at $routeStubPath");
            return;
        }

        if (!File::exists($webRoutesPath)) {
            $this->error("Web routes file not found at $webRoutesPath");
            return;
        }

        $routeToAdd = File::get($routeStubPath);

        if (Str::contains(File::get($webRoutesPath), $routeToAdd)) {
            $this->warn('Magic login route is already present in web.php.');
            return;
        }

        File::append($webRoutesPath, "\n" . $routeToAdd);
        $this->info('Magic login route added to web.php.');
    }

    protected function installMigration(): void
    {
        $sourcePath = __DIR__ . '/../../database/migrations/make_name_password_email_nullable_in_users_table.php';
        $migrationDir = database_path('migrations');
        $newMigrationName = $migrationDir . '/' . date('Y_m_d_His') . '_make_name_and_password_nullable_in_users_table.php';

        if (!File::exists($sourcePath)) {
            $this->error("Migration source file not found at $sourcePath");
            return;
        }

        if (!File::exists($migrationDir)) {
            $this->error("Migration directory not found at $migrationDir");
            return;
        }

        if (File::exists($newMigrationName)) {
            $this->warn("Migration file $newMigrationName already exists.");
            return;
        }

        File::copy($sourcePath, $newMigrationName);
        $this->info('make_name_password_email_nullable_in_users_table migration installed.');
    }

    protected function installPhoneMigration(): void
    {
        $sourcePath = __DIR__ . '/../../database/migrations/add_phone_to_users_table.php';
        $migrationDir = database_path('migrations');
        $newMigrationName = $migrationDir . '/' . date('Y_m_d_His') . '_add_phone_to_users_table.php';

        if (!File::exists($sourcePath)) {
            $this->error("Migration source file not found at $sourcePath");
            return;
        }

        if (!File::exists($migrationDir)) {
            $this->error("Migration directory not found at $migrationDir");
            return;
        }

        if (File::exists($newMigrationName)) {
            $this->warn("Migration file $newMigrationName already exists.");
            return;
        }

        File::copy($sourcePath, $newMigrationName);
        $this->info('add_phone_to_users_table migration installed.');
    }

    protected function installVuePage(): void
    {
        $sourcePath = __DIR__ . '/../../resources/js/Pages/Auth/MagicAuth.vue';
        $vuePath = resource_path('js/Pages/Auth/MagicAuth.vue');

        if (!File::exists($sourcePath)) {
            $this->error("MagicAuth Vue page not found at $sourcePath");
            return;
        }

        $vueDir = dirname($vuePath);
        if (!File::exists($vueDir)) {
            File::makeDirectory($vueDir, 0755, true);
            $this->info("Created directory $vueDir");
        }

        if (File::exists($vuePath)) {
            $this->warn("MagicAuth Vue page already exists at $vuePath.");
            return;
        }

        File::copy($sourcePath, $vuePath);
        $this->info('MagicAuth Vue page installed.');
    }

    protected function installIntegrationScript(): void
    {
        $sourcePath = __DIR__ . '/../../resources/js/magicmk_integration_ES6_min.js';

        $destinationPath = resource_path('js/magicmk_integration_ES6_min.js');

        if (!File::exists($sourcePath)) {
            $this->error("Integration script not found at $sourcePath");
            return;
        }

        $destinationDir = dirname($destinationPath);
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
            $this->info("Created directory $destinationDir");
        }

        if (File::exists($destinationPath)) {
            $this->warn("Integration script already exists at $destinationPath.");
            return;
        }

        File::copy($sourcePath, $destinationPath);
        $this->info('magicmk_integration_ES6_min.js script installed.');
    }

    protected function updateEnvFile(): void
    {
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            if (Str::contains($envContent, 'MAGIC_LOGIN_PROJECT_KEY') || Str::contains($envContent, 'MAGIC_LOGIN_API_KEY')) {
                $this->warn('.env file already contains MAGIC_LOGIN_PROJECT_KEY and/or MAGIC_LOGIN_API_KEY.');
                return;
            }

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

        $actionsFolder = app_path('Actions/Fortify');

        if (!File::exists($actionsFolder)) {
            $this->warn("The /app/Actions/Fortify folder does not exist. Ignoring custom user action installation.");
            return;
        }

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
            $this->warn('FortifyServiceProvider.php not found. Ignoring custom user action installation.');
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
