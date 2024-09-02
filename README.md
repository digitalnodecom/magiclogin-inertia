# MagicmkAuthLaravelInertia

**MagicmkAuthLaravelInertia** is a Laravel package that integrates Magicmk authentication with Laravel + Inertia.js projects. It simplifies the setup process, provides a ready-made controller, views, and migration, and allows seamless integration with your existing Laravel application.

## Features

- Easy integration of Magicmk authentication into Laravel + Inertia.js projects.
- Automatic setup of the User model and migration, controller, routes and Vue.js auth page.

## Installation

You can **install the package** via Composer:

```bash
composer require magicmk/magicmk-auth-laravel-inertia
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

