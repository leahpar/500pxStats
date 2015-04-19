<?php

namespace PX500\CoreBundle\Command;

use PX500\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdduserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('px:adduser')
        ->setDescription('Update users and photos')
        ->addArgument('username', InputArgument::REQUIRED, 'user to add')
        ->addOption('photo', null, InputOption::VALUE_NONE, 'if present, get last photo')
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
        $em = $this->getContainer()->get('doctrine')->getManager();

        // input argument
        $username = $input->getArgument('username');

        // set api url
        $url  = $dataService->api_url;
        $url .= '/users';
        $url .= '/show';
        $url .= '?username='.$username;
        $url .= '&consumer_key='.$dataService->api_key;

        // Check if user already exists
        $user = $em->getRepository("PX500CoreBundle:User")->findOneByUsername($username);
        if ($user != null)
        {
            $output->writeln("User $username already exists !");
            exit;
        }

        // call 500px api
        /** @var HttpException $e */
        try {
            $data = $dataService->getDataFromUrl($url);
            $userData = $data['user'];

            // Create new user
            $user = new User();
            $user->setUid($userData['id']);
            $user->setUsername($userData['username']);
            $user->setName($userData['firstname'].' '.$userData['lastname']);
            $user->setPhotosCount($userData['photos_count']);

            // set photos -1 so that last photo will be updated
            if ($input->getOption('photo')) {
                $user->setPhotosCount($userData['photos_count']-1);
            }

            // Persist new user
            $em->persist($user);
            $em->flush();

            $output->writeln('user added');
        }
        catch (HttpException $e)
        {
            switch ($e->getStatusCode()) {
                case "404":
                    $output->writeln("500px : User not found");
                    break;
                case "401":
                case "403":
                    $output->writeln("500px : Acces denied (check your api key)");
                    break;
                default:
                    $output->writeln("500px : error " . $e->getStatusCode());
                    $output->writeln($url);
            }
        }
    }
}