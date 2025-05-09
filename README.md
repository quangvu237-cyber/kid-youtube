# Laravel 12 Docker

This repository contains a Laravel 12 application configured to run with Docker.

## Requirements (For Docker)
- Stable version of [Docker](https://docs.docker.com/engine/install/)
- Compatible version of [Docker Compose](https://docs.docker.com/compose/install/#install-compose)

## Installation (Docker)

1. Clone this repository:
   ```bash
   git clone git@github.com:lamchu-cmyk/laravel12-docker.git
   cd laravel12-docker
   ```

2. Create a `.env` file from the example:
   ```bash
   cp .env.example .env
   ```

3. Build and start the Docker containers:
   ```bash
   docker-compose up -d
   ```

4. Install PHP dependencies:
   ```bash
   docker-compose exec app composer install
   ```

5. Generate application key:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. Install and build frontend assets:
   ```bash
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

7. Run database migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```

8. Access the application at http://localhost:8080

## Requirements (For Local)
- [PHP v8.2.x (XAMPP)](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download) or [PHP v8.4.x (Laragon)](https://github.com/leokhoa/laragon/releases/download/8.0.0/laragon-wamp.exe)
- [Composer](https://getcomposer.org/download/)
- [Node.js v22.x](https://nodejs.org/en/blog/release/v22.11.0)

## Installation (Local)

1. Clone this repository:
   ```bash
   git clone git@github.com:lamchu-cmyk/laravel12-docker.git
   cd laravel12-docker
   ```

2. Create a `.env` file from the example:
   ```bash
   cp .env.example .env
   ```

3. Install PHP dependencies:
   ```bash
   composer install
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Install and build frontend assets:
   ```bash
   npm install
   npm run build
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Start the local development server:
   ```bash
   php artisan serve
   ```

8. Access the application at http://localhost:8000

## Available Commands

- Run tests:
  ```bash
  docker-compose exec app php artisan test
  ```

- Run Artisan commands:
  ```bash
  docker-compose exec app php artisan <command>
  ```

- Access the container shell:
  ```bash
  docker-compose exec app bash
  ```

## Docker Services

- **laravel-app**: PHP Laravel application
- **node-app**: PHP Laravel application
- **nginx-laravel**: Web server

## Troubleshooting

- If you encounter permission issues, run:
  ```bash
  docker-compose exec app chmod -R 777 storage bootstrap/cache
  ```

- If Laravel is not recognizing environment variables, run:
  ```bash
  docker-compose exec app php artisan config:clear
  ```

## Debug

- Access Laravel Telescope:
  ```bash
  http://your-laravel-url:port/telescope
  ```
- Access Debugbar: Change "APP_DEBUG=true" in .env to activate.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credit
Make by [lamchu-cmyk](https://github.com/lamchu-cmyk) with ❤️