<?php
namespace RtxLabs\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpiredTokensCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('user:tokens:expired:delete')
             ->setDescription('check for expired tokens and reset or delete the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tokenManager = $this->getContainer()->get('rtxlabs.user.token_manager');
        $tokenManager->setOutput($output);
        $tokenManager->checkTokens();
    }
}