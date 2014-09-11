<?php

namespace LOM\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

abstract class FixturesWebTestCase extends WebTestCase {

    protected static $application;

    public function __construct() {
        parent::__construct();
    }

    protected function setUp() {
        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load -n');
    }

    protected static function runCommand($command) {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication() {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

}
