# bilemo_api

requirements :

- docker
- composer
- symfony cli

to install and run the project, run the following commands :

- git clone git@github.com:ThomasLdev/bilemo_api.git
- docker-compose -up
- composer install
- symfony serve -d
- php bin/console doctrine:create:database
- php bin/console doctrine/migrations/migrate
- php bin/console doctrine:fixtures:load

<hr>

please read the documentation in order to see which routes are available :

[LINK]

<hr>

code quality badge :

<a href="https://codeclimate.com/github/ThomasLdev/bilemo_api/maintainability"><img src="https://api.codeclimate.com/v1/badges/1a1733ff9d290cb2c46e/maintainability" /></a>
