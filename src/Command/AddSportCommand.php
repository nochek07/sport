<?php

namespace App\Command;

use App\Entity\{Game, GameBuffer};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputArgument};
use Symfony\Component\Console\Output\OutputInterface;

class AddSportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

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
        $ArrayForGamesID = unserialize(base64_decode($input->getArgument('serialiseObject')));
        if (is_array($ArrayForGamesID)) {

            $findGameBufer = $this->manager
                ->getRepository(GameBuffer::class)
                ->findBy(['id' => $ArrayForGamesID]);

            /**
             * @var GameBuffer $gameBuffer
             */
            foreach ($findGameBufer as $gameBuffer) {
                $sport = $gameBuffer->getLeague()->getSport();
                $diff = $sport->getDiff();
                /**
                 * @var \DateTime $date
                 */
                $date = $gameBuffer->getDate();

                $dateStart = clone $date;
                $dateEnd = clone $date;
                $dateStart->modify("- {$diff} hour");
                $dateEnd->modify("+ {$diff} hour");

                /**
                 * @var Game $findGame
                 */
                $findGame = $this->manager
                    ->getRepository(Game::class)
                    ->findByBuffer($gameBuffer, $dateStart, $dateEnd);
                if (!($findGame instanceof Game)) {
                    $game = new Game();
                    $game->setLeague($gameBuffer->getLeague());
                    $game->setLanguage($gameBuffer->getLanguage());
                    $game->setTeam1($gameBuffer->getTeam1());
                    $game->setTeam2($gameBuffer->getTeam2());
                    $game->setDate($gameBuffer->getDate());
                    $this->manager->persist($game);
                    $gameBuffer->setGame($game);
                } else {
                    $difference = $date->diff($findGame->getDate());
                    if ($difference->invert == 1) {
                        $findGame->setDate($date);
                    }
                    $gameBuffer->setGame($findGame);
                }
                $this->manager->flush();
            }
        }

        $output->writeln([
            'END!'
        ]);
    }
}