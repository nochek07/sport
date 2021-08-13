<?php

namespace App\Command;

use App\Entity\{Game, GameBuffer};
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

    protected static $defaultName = 'app:sport:add';

    /**
     * AddSportCommand constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a new sport.')
            ->setHelp('This command allows you to add a sport...')
            ->addArgument('serialisedData', InputArgument::REQUIRED, 'Serialises data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games = json_decode($input->getArgument('serialisedData'));
        if (is_array($games)) {
            $foundGameBuffers = $this->manager
                ->getRepository(GameBuffer::class)
                ->findBy(['id' => $games]);

            /**
             * @var GameBuffer $gameBuffer
             */
            foreach ($foundGameBuffers as $gameBuffer) {
                $sport = $gameBuffer->getLeague()->getSport();
                $diff = $sport->getDiff();
                /**
                 * @var \DateTimeImmutable $date
                 */
                $date = $gameBuffer->getDate();
                
                $dateStart = $date->modify("- {$diff} hour");
                $dateEnd = $date->modify("+ {$diff} hour");

                /**
                 * @var Game $foundGame
                 */
                $foundGame = $this->manager
                    ->getRepository(Game::class)
                    ->findByBuffer($gameBuffer, $dateStart, $dateEnd);
                if (!($foundGame instanceof Game)) {
                    $game = new Game();
                    $game->setLeague($gameBuffer->getLeague());
                    $game->setLanguage($gameBuffer->getLanguage());
                    $game->setTeam1($gameBuffer->getTeam1());
                    $game->setTeam2($gameBuffer->getTeam2());
                    $game->setDate($gameBuffer->getDate());
                    $this->manager->persist($game);
                    $gameBuffer->setGame($game);
                } else {
                    $difference = $date->diff($foundGame->getDate());
                    if ($difference->invert == 1) {
                        $foundGame->setDate($date);
                    }
                    $gameBuffer->setGame($foundGame);
                }
                $this->manager->flush();
            }
        }

        $output->writeln([
            'Done!'
        ]);
    }
}