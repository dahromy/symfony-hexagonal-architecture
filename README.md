<h1 align="center">
  🐘🎯 Hexagonal Architecture, DDD & TDD in Symfony
</h1>

<p align="center">
    <a href="https://github.com/dahromy"><img src="https://img.shields.io/badge/dahromy-OS-green.svg?style=flat-square" alt="dahromy"/></a>
    <a href="#"><img src="https://img.shields.io/badge/Symfony-5.4-purple.svg?style=flat-square&logo=symfony" alt="Symfony 5.4"/></a>
</p>

<p align="center">
   Example of a <strong>Symfony application using Domain-Driven Design (DDD) and <br /> 
   Test Driver Development (TDD) principles</strong> keeping the code as simple as possible.
  <br />
  <br />
  Take a look, play and have fun with this.
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/stargazers">Stars are welcome 😊</a>
  <br />
  <br />
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/issues">Report a bug</a>
  ·
  <a href="https://github.com/dahromy/symfony-hexagonal-architecture/issues">Request a feature</a>
</p>

## 🚀 Environment Setup

### 🐳 Needed tools

1. PHP7.4, Composer
2. Clone this project: `git clone https://github.com/dahromy/symfony-hexagonal-architecture sf-hexa-example`
3. Move to the project folder: `cd sf-hexa-example`

### 🛠️ Environment configuration

1. Create a local environment file (`cp .env .env.local`) if you want to modify any parameter

### 🔥 Application execution

1. Install all the dependencies and bring up the project with Composer executing: `composer install`

### ✅ Tests execution

1. Install the dependencies if you haven't done it previously: `composer install`
2. Execute PHPUnit and Behat tests: `php bin/phpunit --configuration phpunit.xml.dist`

### 🎯 Hexagonal Architecture

This repository follows the Hexagonal Architecture pattern. Also, it's structured using `modules`.
With this, we can see that the current structure of a Bounded Context is:

```scala
$ tree -L 5 src
    
src
├── Application // The application of our app
│   └── Post // Inside the application layer all is structured by actions
│       └── CreatePost
│           ├── CreatePostCommand.php
│           └── CreatePostUseCase.php
├── Domain // The domain of our app
│   └── Post
│       ├── Post.php // The Aggregate of the Module
│       └── Services
│           └── PostRepositoryInterface.php // The `Interface` of the repository is inside Domain
├── Infrastructure // The infrastructure of our app
│   ├── Controller
│   └── Persistence
│       ├── Doctrine
│       │   └── Post
│       │       ├── PostDoctrineParser.php
│       │       ├── PostDoctrineRepository.php // An implementation of the repository
│       │       └── Post.php
│       ├── InFile
│       │   ├── FilesystemHandler.php
│       │   └── Post
│       │       ├── InFilePostParser.php
│       │       └── InFilePostRepository.php
│       └── InMemory
│           └── Post
│               └── InMemoryPostRepository.php
└── Kernel.php

```

## 🤔 Contributing
There are some things missing (add swagger, improve documentation...), feel free to add this if you want! If you want
some guidelines feel free to contact us :)
