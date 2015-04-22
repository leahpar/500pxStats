<?php

namespace PX500\CoreBundle\Command;

use PX500\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdatedbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('px:updatedb')
        ->setDescription('Update DB : add missing attributes')
        ->addOption('users',   null, InputOption::VALUE_NONE, 'Update User')
        ->addOption('photos',  null, InputOption::VALUE_NONE, 'Update Photo')
        ->addOption('stats',   null, InputOption::VALUE_NONE, '[not implemented] Update UserStat : add photo upload date')
        ->addOption('clean',   null, InputOption::VALUE_NONE, '[not implemented] Clean UserStat : remove useless stats rows (same consecutive values)')
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

        $updatePhotos = false;
        $updateUsers  = false;
        $updateStats  = false;
        $cleanStats   = false;

        // Symfony command
        // http://symfony.com/fr/doc/current/components/console/introduction.html

        // input options
        if ($input->getOption('users')) {
            $updateUsers = true;
            $output->writeln('update users...');
        }
        else if ($input->getOption('photos')) {
            $updatePhotos = true;
            $output->writeln('update photos...');
        }
        else if ($input->getOption('stats')) {
            $updateStats = true;
            $output->writeln('update users stats...');
        }
        else if ($input->getOption('clean')) {
            $cleanStats = true;
            $output->writeln('clean users stats...');
        }
        else {
            $output->writeln('<error>You must choose an action</error>');
            exit;
        }

        $dataService->updatedb($updateUsers, $updatePhotos, $updateStats, $cleanStats);

        // TODO : log error
        // http://symfony.com/doc/current/cookbook/console/logging.html#logging-non-0-exit-statuses

        $output->writeln('<info>done</info>');
   }
}