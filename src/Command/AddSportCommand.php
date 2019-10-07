<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputArgument};
use Symfony\Component\Console\Output\OutputInterface;

class AddSportCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    // bin/console app:sport:add '1111111111111111' > /dev/null 2>&1 &
    protected static $defaultName = 'app:sport:add';

    protected function configure()
    {
        $this
            ->setDescription('Add a new sport.')
            ->setHelp('This command allows you to add a sport...')
            ->addArgument('serialiseObject', InputArgument::REQUIRED, 'Serialise object')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'New Sport',
            '============',
            $input->getArgument('serialiseObject'),
        ]);

        $this->logger->error('An error occurred');
    }
}