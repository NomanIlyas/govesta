rsync -r ./ /var/www/api/
cd /var/www/api
sudo chmod -R ug+rwx storage bootstrap/cache
composer install
php artisan migrate