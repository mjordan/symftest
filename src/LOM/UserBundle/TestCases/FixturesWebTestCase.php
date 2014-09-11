<?php

namespace LOM\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

/**
 * Extend the WebTestCase class, and add some fixtures.
 */
abstract class FixturesWebTestCase extends WebTestCase
{
    /**
     * The application, so that console commands can be run
     * inside the test setup.
     *
     * @var Application $application
     */
    protected static $application;

    /**
     * Construct a test case.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Do the setup - drop and recreate the schema and load the
     * test data.
     */
    protected function setUp()
    {
        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load -n');
    }

    /**
     * Run a command.
     *
     * @param string $command
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * Treat $application like a singleton and return it.
     *
     * @return Application
     */
    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

}
