######https://laravel.com/docs/migrations
#####Generating Migrations
`php artisan make:migration create_flights_table`
####Running Migrations
`php artisan migrate`
#####Drop All Tables & Migrate
`php artisan migrate:fresh`

`php artisan migrate:fresh --seed`

####The Public Disk
`php artisan storage:link`