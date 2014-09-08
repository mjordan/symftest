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

    private function exec($cmd, $args, $output) {
        $command = $this->getApplication()->find($cmd);
        $args['command'] = $cmd;
        $input = new ArrayInput($args);
        $rc = $command->run($input, $output);
        return $rc;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This command will purge all data from the database. Continue y/N ?', false);
        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $this->exec('doctrine:schema:drop', array('--force' => true), $output);
        $this->exec('doctrine:schema:create', array(), $output);
        $this->exec('doctrine:fixtures:load', array(), $output);
        $this->exec('cache:clear', array(), $output);
        $this->exec('doctrine:cache:clear-metadata', array(), $output);
        $this->exec('doctrine:cache:clear-query', array(), $output);
        $this->exec('doctrine:cache:clear-result', array(), $output);
    }

}
