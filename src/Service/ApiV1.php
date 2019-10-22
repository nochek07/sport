<?php

namespace App\Service;

use App\Entity\{Game, GameBuffer, Language, League, Source, Sport, Team};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class ApiV1
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var PropertyBuilder
     */
    private $propertyBuilder;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     * @param KernelInterface $kernel
     */
    public function __construct(
        EntityManagerInterface $manager,
        PropertyBuilder $propertyBuilder,
        KernelInterface $kernel
    ) {
        $this->manager = $manager;
        $this->propertyBuilder = $propertyBuilder;
        $this->kernel = $kernel;
    }

    /**
     * Add Game
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function addGame(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['events'])) {

            foreach ($data['events'] as $event) {
                $this->propertyBuilder->addDataIn(PropertyBuilder::LANGUAGE, $event['lang']);
                $this->propertyBuilder->addDataIn(PropertyBuilder::SPORT, $event['sport']);
                $this->propertyBuilder->addDataIn(PropertyBuilder::LEAGUE, [$event['league'], $event['sport']]);
                $this->propertyBuilder->addDataIn(PropertyBuilder::TEAM, [$event['team1'], $event['sport']]);
                $this->propertyBuilder->addDataIn(PropertyBuilder::TEAM, [$event['team2'], $event['sport']]);
                $this->propertyBuilder->addDataIn(PropertyBuilder::SOURCE, $event['source']);
            }
            $this->propertyBuilder->fillingData();


            $ArrayForGames = [];
            foreach ($data['events'] as $event) {

                $lang = $this->propertyBuilder->lookForData(PropertyBuilder::LANGUAGE, $event['lang']);
                if (!($lang instanceof Language)) {
                    $lang = $this->propertyBuilder->insertEntity(PropertyBuilder::LANGUAGE, $event['lang']);
                }

                $source = $this->propertyBuilder->lookForData(PropertyBuilder::SOURCE, $event['source']);
                if (!($source instanceof Source)) {
                    $source = $this->propertyBuilder->insertEntity(PropertyBuilder::SOURCE, $event['source']);
                }

                $sport = $this->propertyBuilder->lookForData(PropertyBuilder::SPORT, $event['sport']);
                if (!($sport instanceof Sport)) {
                    $sport = $this->propertyBuilder->insertEntity(PropertyBuilder::SPORT, $event['sport']);
                    $league = $this->propertyBuilder->insertEntity(PropertyBuilder::LEAGUE, $event['league'], $sport);
                    $team1 = $this->propertyBuilder->insertEntity(PropertyBuilder::TEAM, $event['team1'], $sport);
                    $team2 = $this->propertyBuilder->insertEntity(PropertyBuilder::TEAM, $event['team2'], $sport);
                } else {
                    $league = $this->propertyBuilder->lookForData(PropertyBuilder::LEAGUE, $event['league'], $sport);
                    if (!($league instanceof League)) {
                        $league = $this->propertyBuilder->insertEntity(PropertyBuilder::LEAGUE, $event['league'], $sport);
                    }

                    $team1 = $this->propertyBuilder->lookForData(PropertyBuilder::TEAM, $event['team1'], $sport);
                    if (!($team1 instanceof Team)) {
                        $team1 = $this->propertyBuilder->insertEntity(PropertyBuilder::TEAM, $event['team1'], $sport);
                    }

                    $team2 = $this->propertyBuilder->lookForData(PropertyBuilder::TEAM, $event['team2'], $sport);
                    if (!($team2 instanceof Team)) {
                        $team2 = $this->propertyBuilder->insertEntity(PropertyBuilder::TEAM, $event['team2'], $sport);
                    }
                }

                $filter = [
                    'language' => $lang,
                    'league' => $league,
                    'team1' => $team1,
                    'team2' => $team2,
                    'date' => new \DateTime($event['date']),
                    'source' => $source
                ];

                // Game_Buffer
                $gameBuffer = $this->manager
                    ->getRepository(GameBuffer::class)
                    ->findOneBy($filter);
                if (!($gameBuffer instanceof GameBuffer)) {
                    $gameBuffer = new GameBuffer();
                    $gameBuffer->setLeague($filter['league']);
                    $gameBuffer->setLanguage($filter['language']);
                    $gameBuffer->setTeam1($filter['team1']);
                    $gameBuffer->setTeam2($filter['team2']);
                    $gameBuffer->setDate($filter['date']);
                    $gameBuffer->setSource($filter['source']);
                    $this->manager->persist($gameBuffer);

                    $ArrayForGames[] = $gameBuffer;
                }
            }
            if (sizeof($ArrayForGames) > 0) {
                $this->manager->flush();
                $ArrayForGamesID = [];
                foreach ($ArrayForGames as $gameBuffer) {
                    $ArrayForGamesID[] = $gameBuffer->getId();
                }

                $serializer = base64_encode(serialize($ArrayForGames));
                $process = Process::fromShellCommandline('bin/console app:sport:add "' . $serializer . '" > /dev/null 2>&1 &');
                $process->setWorkingDirectory($this->kernel->getProjectDir());
                $process->run();
            }

            return ['success' => 1];
        }
        return ['success' => 0];
    }

    /**
     * Get random game
     * Get random game by filter
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function random(Request $request): array
    {
        $result = [];

        /**
         * @var Game $randGame
         */
        $randGame = $this->manager
            ->getRepository(Game::class)
            ->getRandom();

        if ($randGame instanceof Game) {
            $result = [
                "game" => [
                    "lang" => $randGame->getLanguage()->getName(),
                    "sport" => $randGame->getLeague()->getSport()->getName(),
                    "league" => $randGame->getLeague()->getName(),
                    "team1" => $randGame->getTeam1()->getName(),
                    "team2" => $randGame->getTeam2()->getName(),
                    "date" => $randGame->getDate()->format('Y-m-d G:i:s'),
                ],
                "buffers" => []
            ];

            $filter = [];
            if (!is_null($request->query->get('source'))) {
                $filter['source'] = $request->query->get('source');
            }
            if (!is_null($request->query->get('start')) && !is_null($request->query->get('end'))) {
                $filter['start'] = $request->query->get('start');
                $filter['end'] = $request->query->get('end');
            }

            $gamesBuffer = $this->manager
                ->getRepository(GameBuffer::class)
                ->findByGame($randGame, $filter);

            /**
             * @var GameBuffer[] $gamesBuffer
             */
            foreach ($gamesBuffer as $gameBuffer) {
                $result["buffers"][] = [
                    "lang" => $gameBuffer->getLanguage()->getName(),
                    "sport" => $gameBuffer->getLeague()->getSport()->getName(),
                    "league" => $gameBuffer->getLeague()->getName(),
                    "team1" => $gameBuffer->getTeam1()->getName(),
                    "team2" => $gameBuffer->getTeam2()->getName(),
                    "date" => $gameBuffer->getDate()->format('Y-m-d G:i:s'),
                    "source" => $gameBuffer->getSource()->getName(),
                ];
            }
        }

        return $result;
    }
}