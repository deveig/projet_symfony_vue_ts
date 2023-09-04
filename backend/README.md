# Cooking recipe web page

Development of a cooking recipe web page.

## Project

Back-end with [Symfony](https://symfony.com/doc/current/setup.html). PHP 8.2.9, Composer 2.5.8. Database with phpMyAdmin of [MAMP](https://www.mamp.info/en/downloads/) and MySQL.

## Database with phpMyAdmin of MAMP

Install packages with command line `symfony composer install`.
Configure the 'php.ini' file with 'extension=pdo_mysql'.
Create the database with name 'recipe' in phpMyAdmin of MAMP, with command line `php bin/console doctrine:database:create`. DATABASE_URL is in the '.env' file. Port number to MySQL is 8889. See the webstart page of MAMP. Then create table 'ingredient' in database 'recipe' with command lines `php bin/console make:migration` then `php bin/console doctrine:migrations:migrate`.

## Run application

Install packages with command line `symfony composer install`. 
Start MySQL server with MAMP. 
Run application with command line `symfony server:start`.

## Run tests

Install packages with command line `symfony composer install`.
Create tests database with command line `php bin/console --env=test doctrine:database:create`. Then add tables with command line `php bin/console --env=test doctrine:schema:create`.
Load 'fixtures data' into database with command line `php bin/console --env=test doctrine:fixtures:load`.
Run the tests with command line `php bin/phpunit tests/Controller/RecipeControllerTest.php`.