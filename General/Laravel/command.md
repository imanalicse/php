#####Installation Via Composer
composer create-project laravel/laravel example-app
cd example-app
php artisan serve
####The Laravel Installer
composer global require laravel/installer
laravel new example-app
php artisan serve

###configuration

####Maintenance Mode
php artisan down
####Bypassing Maintenance Mode
php artisan down --secret="1630542a-246b-4b66-afa1-dd72a4c43515"

####Disabling Maintenance Mode
php artisan up


php artisan make:model Survey -m
