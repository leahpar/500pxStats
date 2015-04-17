<?php

namespace PX500\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('px:update')
        ->setDescription('Update users and photos')
        //->addArgument('name', InputArgument::OPTIONAL, 'Qui voulez vous saluer??')
        //->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataService = $this->getContainer()->get('service.data');
        $dataService->updateAll();
        $output->writeln('');
        $output->writeln('ok');
    }
}