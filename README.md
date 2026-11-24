# Arrocy EZ Dashboard

Arrocy EZ Dashboard is a Laravel admin panel with a modular package structure. The core dashboard manages users, settings, and files, while Arrocy add-on packages provide product-specific capabilities.

## Features

- Authentication, registration, password reset, and email verification
- User and administrator management
- Application settings and file management
- Modular add-ons, maintained as Composer packages:
  - **AI Bot** (`arrocy/ai-bot`)
  - **Phonebook** (`arrocy/phonebook`)
  - **Templates** (`arrocy/template`)
  - **WhatsApp** (`arrocy/whatsapp`)

The add-on packages are in `arrocy/` and are registered with Laravel through Composer package discovery.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- A supported database (SQLite is the default; MySQL is also configured)

## Installation

There is no browser-based installer. Configure the application and database from the command line.

1. Create your local environment file:

   ```sh
   cp .env.example .env
   ```

2. Edit `.env` and set `APP_NAME`, `APP_URL`, and your database connection. The default SQLite connection uses `database/database.sqlite`; leave `DB_DATABASE` unset to use that path. If using MySQL, create the database before continuing and enter its connection details.

3. Install dependencies, generate the application key, and migrate the database (frontend assets are pre-built):

   ```sh
   composer install
   php artisan key:generate
   php artisan migrate --force
   ```

4. Seed countries, default settings, and the initial administrator:

   ```sh
   php artisan db:seed
   ```

   Default administrator credentials are `admin@test.com` and `password`.
   Review `database/seeders/AdminSeeder.php` and replace its development credentials before seeding a production database. Do not publish or reuse development credentials.

5. Configure `MAIL_*` values in `.env` if the application needs to send email. The example file leaves mail credentials blank.

## Module/Addon Package Installation

Add repositories before installing add-on packages with Composer:

```sh
composer config repositories.ez-ai-bot vcs https://github.com/arrocy/ez-ai-bot
composer config repositories.ez-phonebook vcs https://github.com/arrocy/ez-phonebook
composer config repositories.ez-template vcs https://github.com/arrocy/ez-template
composer config repositories.ez-whatsapp vcs https://github.com/arrocy/ez-whatsapp
```

Install add-on packages with Composer:

```sh
composer require arrocy/ai-bot:ez-dev-main
composer require arrocy/phonebook:ez-dev-main
composer require arrocy/template:ez-dev-main
composer require arrocy/whatsapp:ez-dev-main
```

Migrate the database for the add-on packages:

```sh
php artisan migrate
```

## Project structure

| Path | Contents |
| --- | --- |
| `app/` | Core Laravel application code |
| `database/` | Migrations, factories, and seeders |
| `resources/` | Blade views, CSS, and JavaScript |
| `routes/` | Web, API, and console routes |

## Configuration

After installed, login with the default administrator credentials (`admin@test.com` and `password`). Menu Settings, change the default configuration values as needed.

Keep deployment-specific values in `.env`, including database credentials, mail settings, OAuth credentials, and `APP_KEY`. Do not commit `.env` or production secrets. For production, set `APP_ENV=production` and `APP_DEBUG=false`. After changing environment values on a deployed application, refresh Laravel's cached configuration with `php artisan optimize:clear`.
