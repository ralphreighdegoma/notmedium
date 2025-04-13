# Blogqua - Laravel Blog Application

A modern blog application built with Laravel, featuring API authentication with Laravel Passport.

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL or MariaDB
- Node.js & NPM
- Git

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd blogqua
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Environment Configuration

Create a copy of the `.env.example` file:

```bash
cp .env.example .env
```

Update the `.env` file with your database credentials and other configuration:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blogqua
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Create a clean database

Create a new database with the name specified in your `.env` file, then run migrations:

```bash
php artisan migrate:fresh
```

### 7. Set up Laravel Passport

Install Passport:

- skip

Create the OAuth keys directory:

```bash
mkdir -p secrets/oauth
```

Generate OAuth keys:

```bash
php artisan passport:keys --force
```

Move the generated keys to the correct location:

```bash
mv storage/oauth/*.key secrets/oauth/
```

### 8. Seed the database (optional)

If you want to populate the database with test data:

```bash
php artisan db:seed --class=BlogSeeder
```

## Running the Application

### 1. Start the development server

```bash
php artisan serve
```

### 2. Compile assets for development

```bash
npm run dev
```

The application should now be accessible at http://localhost:8000

This application uses Laravel Passport for API authentication. Here's how to use it:

### Storage Link Issues

If you encounter issues with file uploads, make sure the storage link is created:

```bash
php artisan storage:link
```

## License

This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
