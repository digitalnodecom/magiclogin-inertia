# MagicmkAuthLaravelInertia

**MagicmkAuthLaravelInertia** is a Laravel package that integrates Magicmk authentication with Laravel + Inertia.js projects. It simplifies the setup process, provides a ready-made controller, views, and migration, and allows seamless integration with your existing Laravel application.

## Features

- Easy integration of Magicmk authentication into Laravel + Inertia.js projects.
- Automatic setup of the User model and migration, controller, routes and Vue.js auth page.

## Installation

You can **install the package** via Composer:

```bash
composer require magicmk/digitalnode-magiclogin-inertia
```

After the installation, **run this command** to install the needed files:

```bash
php artisan magicauth:install
```

Once the installation finishes, make sure to **refresh the migrations**:

```bash
php artisan migrate:refresh
```

Remember to add the project id (slug) and project api key from your magic mk project to your **.env**:

```bash
MAGIC_LOGIN_PROJECT_KEY=""
MAGIC_LOGIN_API_KEY=""
```

## Customization
Feel free to customize any of the files we install or overwrite:

```bash
/database/migrations/..._create_users_table.php
/app/Models/User.php
/Http/Controllers/MagicAuthController.php
/routes/web.php
/resources/js/Pages/Auth/MagicAuth.vue
/resources/js/Pages/magicmk_integration.js
```

## Contributing
Contributions are welcome!
Please feel free to submit a Pull Request or open an Issue if you find a bug or have a feature request.

## Credits
Author: Dushan Cimbaljevic
Email: dushan@digitalnode.com
