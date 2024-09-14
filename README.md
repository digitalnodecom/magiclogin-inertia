# magiclogin-inertia

<a href="https://packagist.org/packages/digitalnode/magiclogin-inertia"><img src="https://img.shields.io/packagist/v/digitalnode/magiclogin-inertia" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/digitalnode/magiclogin-inertia"><img src="https://img.shields.io/packagist/dt/digitalnode/magiclogin-inertia" alt="Total Downloads"></a>

<a href="https://magic.mk">www.magic.mk</a>

**magiclogin-inertia** is a Laravel package that integrates magic.mk authentication with Laravel + Inertia.js
projects. It simplifies the setup process, provides a ready-made controller, views, and migration, and allows seamless
integration with your existing Laravel applications.

## Features

- Easy integration of magic.mk authentication into Laravel + Inertia.js projects.
- Automatic setup of the User model and migration, controller, routes and Vue.js auth page.

## Installation

You can **install the package** via Composer:

```bash
composer require digitalnode/magiclogin-inertia
```

After the installation, **run this command** to install the needed files:

```bash
php artisan magiclogin:install
```

Once the installation finishes, make sure to **run the migrations**:

```bash
php artisan migrate
```

After the installation, the **/magic-login route** leads to the magic login page.
The User model **"password" and "name" field are now nullable.**

Remember to add the project id (slug) and project api key from your magic.mk project to your **.env**:

```bash
MAGIC_LOGIN_PROJECT_KEY=""
MAGIC_LOGIN_API_KEY=""
```

## Customization

Feel free to customize any of the files we install or change:

```bash
/database/migrations/..._make_name_and_password_nullable_in_users_table.php
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
