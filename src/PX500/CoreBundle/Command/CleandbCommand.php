<?php

namespace PX500\CoreBundle\Command;

use PX500\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CleandbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('px:cleandb')
        ->setDescription('Clean DB : add missing attributes')
        ->addOption('users', null, InputOption::VALUE_NONE, 'if present, update table User only')
        ->addOption('photos', null, InputOption::VALUE_NONE, 'if present, update table Photo only')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataService = $this->getContainer()->get('service.data');

        // input options
        if ($input->getOption('users')) {
            $updateUsers = true;
            $updatePhotos = false;
            $output->writeln('update users...');
        }
        else if ($input->getOption('photos')) {
            $updateUsers = false;
            $updatePhotos = true;
            $output->writeln('update photos...');
        }
        else {
            $updateUsers = true;
            $updatePhotos = true;
            $output->writeln('update db...');
        }

        $dataService->cleandb($updateUsers, $updatePhotos);
        $output->writeln('done');
   }
}