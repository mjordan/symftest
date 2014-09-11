<?php

/*
 * Copyright (C) 2014 mjoyce
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace LOM\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Doctrine\Bundle\DoctrineBundle\Registry;
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
     * Database access
     *
     * @var Registry $doctrine
     */
    protected static $doctrine;

    /**
     * Construct a test case.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a doctrine instance.
     * 
     * @return Registry
     */
    public function getDoctrine()
    {
        if (null === self::$doctrine) {
            self::bootKernel();
            $this->doctrine = static::$kernel->getContainer()->get('doctrine');
        }
        return $this->doctrine;
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
