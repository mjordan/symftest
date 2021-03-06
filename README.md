Installing the symftest Symfony application
===========================================

The instructions below describe how to use Composer to install Symfony on a Linux or OSX machine. Before running Composer to install Symfony, clone the symftest repo, as outlined below.

Prerequisites
-------------

1. PHP 5.3.3 or higher
1. git
1. A relational database management system such as MySQL or PostgreSQL
1. PHP's intl extension, if it isn't already installed on your system.
1. PHPUnit for running tests (optional)

Installation instructions
-------------------------

1. git clone https://github.com/ubermichael/symftest.git
1. cd symftest
1. curl -s http://getcomposer.org/installer | php
1. php composer.phar install [You will be asked to create a database in this step]
1. php app/check.php
1. [Optional but recommended] Open a web browser and go to  http://localhost/path-to-symtest/web/config.php
1. Change permission to allow all users to write to app/cache and app/logs (e.g. chmod 666 app/cache and chmod 666 app/logs)
1. php app/console doctrine:database:create
1. php app/console doctrine:schema:update --force
1. php app/console doctrine:fixtures:load
1. Go to http://localhost/symftest/web/login and log in with admin@example.com / supersecret
1. Run tests: from the command inside the symftest directory run `phpunit -c app`
