<!--suppress HtmlDeprecatedAttribute -->
<p align="center">
    <img src="public/assets/hexagon.jpg" alt="hexagon">
</p>

<h1 align="center">
  ๐๐ฏ Hexagonal Architecture, DDD & TDD in Symfony
</h1>

<p align="center">
    <a href="https://github.com/dahromy"><img src="https://img.shields.io/badge/dahromy-OS-green.svg?style=flat-square" alt="dahromy"/></a>
    <a href="#"><img src="https://img.shields.io/badge/Symfony-5.4-purple.svg?style=flat-square&logo=symfony" alt="Symfony 5.4"/></a>
</p>

<p align="center">
   Example of a <strong>Symfony application using Domain-Driven Design (DDD) and <br /> 
   Test Driver Development (TDD) principes</strong> keeping the code as simple as possible.
  <br />
  <br />
  Take a look, play and have fun with this.
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/stargazers">Stars are welcome ๐</a>
  <br />
  <br />
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/issues">Report a bug</a>
  ยท
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/issues">Request a feature</a>
</p>

## ๐ Environment Setup

This project is made with [Symfony][1] 5.4.

### ๐ณ Needed tools

1. PHP 7.4 or higher;
2. Composer
3. PDO-MySQL PHP extension enabled;
4. and the [usual Symfony application requirements][2].
5. NodeJS v14.*.
6. Clone this project: `git clone https://github.com/dahromy/symfony-hexagonal-architecture sf-hexa-example`
7. Move to the project folder: `cd sf-hexa-example`

### ๐ ๏ธ Environment configuration

1. Create a local environment file (`cp .env .env.local`) if you want to modify any parameter

### ๐ฅ Application execution

1. Install the backend dependencies: `composer install`.
3. Create database & tables with `php bin/console d:d:c` then `php bin/console make:migration`
   and `php bin/console migration:migrate` or force with `php bin/console d:s:u -f`
5. Install the fronted dependencies: `yarn install` or `npm install`.
6. For the development purpose, run `yarn watch` or `npm run watch`. For the production version, run `yarn build`
   or `npm run build`.
7. Start the server with Symfony: `symfony serve`.
   Then access the application in your browser at the given URL ([https://localhost:8000](https://localhost:8000) by
   default).
   If you don't have the Symfony binary installed, run `php -S localhost:8000 -t public/`
   to use the built-in PHP web server or [configure a web server][3] like
   Apache to run the application.

### โ Tests execution

1. Install the dependencies if you haven't done it previously: `composer install`
2. Execute PHPUnit tests: `php bin/phpunit --configuration phpunit.xml.dist`

### ๐ฏ Hexagonal Architecture

This repository follows the Hexagonal Architecture pattern. Also, it's structured using `modules`.
With this, we can see that the current structure of a Bounded Context is:

```scala
$ tree -L 5 src
    
src
โโโ Application // The application layer of our app
โ   โโโ Post // Inside the application layer all is structured by actions
โ       โโโ Create
โ           โโโ CreatePostCommand.php
โ           โโโ CreatePostUseCase.php
โโโ Domain // The domain layer of our app
โ   โโโ Post
โ       โโโ Post.php // The Aggregate of the Module
โ       โโโ Repository
โ           โโโ PostRepositoryInterface.php // The `Interface` of the repository is inside Domain
โโโ Infrastructure // The layer infrastructure of our app
โ   โโโ Controller
โ   โโโ Persistence
โ       โโโ Doctrine
โ       โ   โโโ Post
โ       โ       โโโ PostDoctrineParser.php
โ       โ       โโโ PostDoctrineRepository.php // An implementation of the repository
โ       โ       โโโ Post.php
โ       โโโ InFile
โ       โ   โโโ FilesystemHandler.php
โ       โ   โโโ Post
โ       โ       โโโ InFilePostParser.php
โ       โ       โโโ InFilePostRepository.php
โ       โโโ InMemory
โ           โโโ Post
โ               โโโ InMemoryPostRepository.php
โโโ Kernel.php

```

## ๐ค Contributing

There are some things missing (add some features: exception, ui, improve documentation...), feel free to add this if you
want! If you want
some guidelines feel free to contact us :)

[1]: https://symfony.com/doc/5.4/index.html

[2]: https://symfony.com/doc/5.4/setup.html#technical-requirements

[3]: https://symfony.com/doc/5.4/setup/web_server_configuration.html
