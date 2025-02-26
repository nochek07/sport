<?php

namespace App\Command;

use App\Service\ApiV1;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

class AddSportCommand extends Command
{
    private ApiV1 $api;

    protected static $defaultName = 'app:sport:add';

    /**
     * AddSportCommand constructor.
     */
    public function __construct(ApiV1 $api)
    {
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