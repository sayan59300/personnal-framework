# README #

# ITVAL Framework #

### What is this repository for? ###

* Basic framework PHP7 with minimal resources for web development
* Version 4.0.0 (old version Accessiwork ver.3.8.0)
* Licence (LGPL-2.1 or GPL-3.0+)

### How do I get set up? ###

* Installation : 
    - Download sources
    - Copy the files in your working folder
    - Execute on the command line : composer update
    - Application index folder /public
* Configuration
    - Edit the config file : /src/config/config.php and add your site configuration
    - For the production version : modify the version constant with "prod"
* Production dependencies
    - php: ^7.1
    - monolog/monolog: ^1.22
    - robmorgan/phinx: ^0.8.1
    - evenement/evenement: ^3.0
    - swiftmailer/swiftmailer: ^6.0
    - pagerfanta/pagerfanta: ^1.0
    - slim/slim: ^3.9
* Development dependencies
    - phpunit/phpunit: ^6.1
    - squizlabs/php_codesniffer: 3.*
* Database configuration
    - Edit the config file : /src/config/config.php and add your BDD configuration
* Swiftmailer configuration
    - Edit the config file : /src/config/config.php and add your configuration
* Routing
    - File : /public/index.php
```php
<?php
    $app->get('/route', Itval\Controllers\YourController::class . ':method');
    $app->post('/route', Itval\Controllers\YourController::class . ':method');
    $app->put('/route', Itval\Controllers\YourController::class . ':method');
    $app->patch('/route', Itval\Controllers\YourController::class . ':method');
    $app->delete('/route', Itval\Controllers\YourController::class . ':method');
```
* How to run tests : Command-line ./vendor/bin/phpunit

### Who do I talk to? ###

* Repo owner or admin
    - ITVAL Society <contact@itval.fr>
    - Nicolas BUFFART <concepteur-developpeur@nicolas-buffart.fr>