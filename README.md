# README #

# Accessiwork #

### What is this repository for? ###

* Basic framework PHP7 with minimal resources for web development
* Version 3.6.1

### How do I get set up? ###

* Installation : 
    - Download sources
    - Copy the files in your working folder
* Configuration
    - Edit the config file : /src/config/config.php and add your site configuration
    - For the production version : modify the version constant with "prod"
* Dependencies production
    - php: ^7.1
    - monolog/monolog: ^1.22
    - robmorgan/phinx: ^0.8.1
    - evenement/evenement: ^3.0
    - swiftmailer/swiftmailer: ^6.0
    - guzzlehttp/psr7: ^1.4
    - http-interop/response-sender: ^1.0
* Dependencies development
    - phpunit/phpunit: ^6.1
    - digitalnature/php-ref: ^1.2
    - squizlabs/php_codesniffer: 3.*
* Database configuration
    - Edit the config file : /src/config/config.php and add your BDD configuration
* Routing
    - Edit /src/routing/routing.php to add routes
    - Edit /src/routing/parameters.php to add parameter type
* How to run tests : Command-line ./vendor/bin/phpunit

### Who do I talk to? ###

* Repo owner or admin
    - Nicolas BUFFART <concepteur-developpeur@nicolas-buffart.fr>