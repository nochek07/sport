<?php

namespace App\Command;

use App\Service\ApiV1;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

class AddSportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ApiV1
     */
    private $api;

    protected static $defaultName = 'app:sport:add';

    /**
     * AddSportCommand constructor.
     *
     * @param EntityManagerInterface $manager
     * @param ApiV1 $api
     */
    public function __construct(EntityManagerInterface $manager, ApiV1 $api)
    {
        $this->manager = $manager;
        $this->api = $api;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a new sport.')
            ->setHelp('This command allows you to add a sport...')
            ->addArgument('serialisedData', InputArgument::REQUIRED, 'Serialised data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $games = json_decode($input->getArgument('serialisedData'));
            if (is_null($games)) {
                throw new \Exception('Argument is not a array');
            }
            if (is_array($games)) {
                $this->api->addGamesByArray($games);
            }
            $output->writeln([
                'DONE!'
            ]);
            return 0;
        } catch(\Exception $exception) {
            return 1;
        }
    }
}