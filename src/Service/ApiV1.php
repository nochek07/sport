<?php

namespace App\Service;

use App\Entity\{Game, GameBuffer, GameInterface, Language, League, Source, Sport, Team};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     */
    public function __construct(EntityManagerInterface $manager, PropertyBuilder $propertyBuilder)
    {
        $this->manager = $manager;
        $this->propertyBuilder = $propertyBuilder;
    }

    /**
     * Add Game
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
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

                $date = new \DateTime($event['date']);

                $filter = [
                    'language' => $lang,
                    'league' => $league,
                    'team1' => $team1,
                    'team2' => $team2,
                    'date' => $date,
                    'source' => $source
                ];

                // Game_Buffer
                $gameBuffer = $this->manager
                    ->getRepository(GameBuffer::class)
                    ->findOneBy($filter);
                if (!($gameBuffer instanceof GameBuffer)) {
                    /**
                     * @var GameBuffer $gameBuffer
                     */
                    $gameBuffer = $this->fillGame(new GameBuffer(), $filter);
                    $gameBuffer->setSource($source);
                    $this->manager->persist($gameBuffer);
                }
                $this->manager->flush();

                $diff = $sport->getDiff();
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
                    /**
                     * @var Game $game
                     */
                    $game = $this->fillGame(new Game(), $filter);
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

    /**
     * Fill GameInterface
     *
     * @param GameInterface $object
     * @param GameInterface|array $data
     *
     * @return GameInterface
     */
    public function fillGame(GameInterface $object, $data): GameInterface
    {
        if ($data instanceof GameInterface) {
            $object->setLeague($data->getLeague());
            $object->setLanguage($data->getLanguage());
            $object->setTeam1($data->getTeam1());
            $object->setTeam2($data->getTeam2());
            $object->setDate($data->getDate());
        } else {
            $object->setLeague($data['league']);
            $object->setLanguage($data['language']);
            $object->setTeam1($data['team1']);
            $object->setTeam2($data['team2']);
            $object->setDate($data['date']);
        }
        return $object;
    }
}