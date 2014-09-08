<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOM\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('lom:purge')
                ->setDescription('Purge *ALL* data from the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This command will purge all data from the database. Continue y/N ?', false);
        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $command = $this->getApplication()->find('doctrine:schema:drop');
        $args = array(
            'command' => 'doctrine:schema:drop',
            '--force' => true,
        );
        $input = new ArrayInput($args);
        $rc = $command->run($input, $output);

        $command = $this->getApplication()->find('doctrine:schema:create');
        $args = array(
            'command' => 'doctrine:schema:create',
        );
        $input = new ArrayInput($args);
        $rc = $command->run($input, $output);

        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $args = array(
            'command' => 'doctrine:fixtures:load',
        );
        $input = new ArrayInput($args);
        $rc = $command->run($input, $output);


        // clear any doctrine caches  
        //exec('php /full/path/your/site/root/app/console doctrine:cache:clear-metadata --env=test');
        //exec('php /full/path/your/site/root/app/console doctrine:cache:clear-query --env=test');
        //exec('php /full/path/your/site/root/app/console doctrine:cache:clear-result --env=test');
    }

}
