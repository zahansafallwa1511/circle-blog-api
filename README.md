# circle-blog-api

This is a simple blog api with very few features like authentication, authorization, resource
controller, middleware and tests. This piece of code is not production ready and intended for representing coding practice only.

Technology:
Laravel: 8.75
PHP: >7.3
Mariadb: 10.6

How to run:
composer install
composer dump-autoload
php artisan key:generate
Generate passport client: php artisan passport:client --passsword
cp sample.env .env
php artisan migrate
php artisan db:seed

To test:
./vendor/bin/phpunit {optional: path_to_test_file}

