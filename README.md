# Bilemo Api v1

<h3>Requirements : </h2>

- (optionnal) Docker : https://docs.docker.com/get-docker/
- (optionnal) Symfony CLI : https://symfony.com/download
- composer : https://getcomposer.org/download/

<h3>Install in command lines : </h3>

- git clone git@github.com:ThomasLdev/bilemo_api.git
- composer install
- (optionnal) docker-compose up -d
- symfony serve -d
- php bin/console doctrine:create:database
- php bin/console doctrine:migrations:migrate
- php bin/console doctrine:fixtures:load

Note : Using the docker setup, the DATABASE_URL parameter is already set properly in the .env for demo purposes. It would obviously not be the case in a production context

<hr>

<h3>Documentation :</h3>

To be found <a href="https://localhost:8000/api/doc">here</a>

<hr>

<h3>Code quality badge :

<a href="https://codeclimate.com/github/ThomasLdev/bilemo_api/maintainability" target="_blank"><img src="https://api.codeclimate.com/v1/badges/1a1733ff9d290cb2c46e/maintainability" /></a>

<hr>

<h3>UML diagrams</h3>

To be found in pdf at the project's root
