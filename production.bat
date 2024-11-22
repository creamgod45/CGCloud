REM config/app.php debugbar comment
REM public copy to root
REM Remove all hot file is laravel dected local server or server assets
REM DONT UPLOAD .env.* will can be download file
REM DONT UPLOAD Unless File
REM docker: .env need set WWWGROUP WWWUSER
REM docker-composer.yml need set user: root
@echo off
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan optimize:clear
php artisan config:clear &&  composer dump-autoload -o
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
npm install
npm run build
echo Done
