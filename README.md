# Bilemo Api v1

<h3>Requirements : </h2>

- (optionnal) Docker : <a href="https://docs.docker.com/get-docker/" target="_blank">Download Docker</a>
- Symfony CLI : <a href="https://symfony.com/download" target="_blank">Download Symfony CLI</a>
- Composer : <a href="https://getcomposer.org/download/" target="_blank">Download Composer</a>
- OpenSSL : <a href="https://www.openssl.org/source/" target="_blank">Download OpenSSL</a>

<h3>Install in command lines : </h3>

- git clone git@github.com:ThomasLdev/bilemo_api.git p07_tlefebvre
- composer install
- (optionnal) docker-compose up -d
- symfony serve -d
- php bin/console doctrine:create:database
- php bin/console doctrine:migrations:migrate
- php bin/console doctrine:fixtures:load

Note 1 : Using the docker setup, the DATABASE_URL parameter is already set properly in the .env for demo purposes. It would obviously not be the case in a production context <br>
Note 2 : Please follow the <a href="https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#getting-started">lexik bundle doc</a> in order to generate your public and private ssh keys and use the API : 

<hr>

<h3>Documentation :</h3>

To be found <a href="https://localhost:8000/api/doc">here</a>

<hr>

<h3>Code quality badge :
<br />
<a href="https://codeclimate.com/github/ThomasLdev/bilemo_api/maintainability"><img src="https://api.codeclimate.com/v1/badges/1a1733ff9d290cb2c46e/maintainability" /></a>

<hr>

<h3>UML diagrams</h3>

To be found in pdf at the project's root
