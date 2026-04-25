# Installation

This guide covers installing PrismPath in a new Laravel 10 project or an existing Laravel application.

## Install From An Empty Folder

Create Laravel 10:

```bash
composer create-project laravel/laravel:^10.0 .
```

Install PrismPath:

```bash
composer require aetherpulse/prismpath
```

Prepare the app:

```bash
cp .env.example .env
php artisan key:generate
```

SQLite setup:

```bash
touch database/database.sqlite
```

`.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

MySQL setup:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ultraclarity
DB_USERNAME=root
DB_PASSWORD=
```

Publish and migrate:

```bash
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate
```

Seed demo data:

```bash
php artisan ultraclarity:seed
```

Run the app:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000/prismpath
http://127.0.0.1:8000/ultraclarity/dashboard
```

## Install In An Existing App

```bash
composer require aetherpulse/prismpath
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate
```

Add the tracker to your layout:

```blade
<!doctype html>
<html>
<body>
    @yield('content')

    @prismpath
</body>
</html>
```

## Dashboard Auth

Default package auth is HTTP basic auth:

```env
ULTRACLARITY_DASHBOARD_AUTH=true
ULTRACLARITY_DASHBOARD_EMAIL=admin@prismpath.test
ULTRACLARITY_DASHBOARD_PASSWORD=password
```

For production, set a strong password or replace `config('ultraclarity.middleware')` with your own admin middleware.

## Build Assets

The dashboard view uses CDN Vue and Chart.js for immediate package usability. If your host app compiles assets, keep Tailwind configured to scan:

```js
'./packages/ultraclarity/analytics/resources/**/*.blade.php',
'./packages/ultraclarity/analytics/resources/**/*.js',
```

Then run:

```bash
npm install
npm run build
```

