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
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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
                $lang = $this->getOrInsertLanguage($event['lang']);
                $sport = $this->getOrInsertSport($event['sport']);
                $league = $this->getOrInsertLeague($event['league'], $sport);
                $team1 = $this->getOrInsertTeam($event['team1'], $sport);
                $team2 = $this->getOrInsertTeam($event['team2'], $sport);
                $source = $this->getOrInsertSource($event['source']);

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
     * Get or insert Language
     *
     * @param string $name
     *
     * @return Language
     */
    public function getOrInsertLanguage(string $name): Language
    {
        $lang = $this->manager
            ->getRepository(Language::class)
            ->findOneBy(['name' => $name]);
        if (!($lang instanceof Language)) {
            $lang = new Language();
            $lang->setName($name);
            $this->manager->persist($lang);
        }
        return $lang;
    }

    /**
     * Get or insert Sport
     *
     * @param string $name
     *
     * @return Sport
     */
    public function getOrInsertSport(string $name): Sport
    {
        $sport = $this->manager
            ->getRepository(Sport::class)
            ->findOneBy(['name' => $name]);
        if (!($sport instanceof Sport)) {
            $sport = new Sport();
            $sport->setName($name);
            $this->manager->persist($sport);
        }
        return $sport;
    }

    /**
     * Get or insert League
     *
     * @param string $name
     * @param Sport $sport
     *
     * @return League
     */
    public function getOrInsertLeague(string $name, Sport $sport): League
    {
        $league = $this->manager
            ->getRepository(League::class)
            ->findOneBy([
                'name' => $name,
                'sport' => $sport
            ]);
        if (!($league instanceof League)) {
            $league = new League();
            $league->setName($name);
            $league->setSport($sport);
            $this->manager->persist($league);
        }
        return $league;
    }

    /**
     * Get or insert Team
     *
     * @param string $name
     * @param Sport $sport
     *
     * @return Team
     */
    public function getOrInsertTeam(string $name, Sport $sport): Team
    {
        $team = $this->manager
            ->getRepository(Team::class)
            ->findOneBy([
                'name' => $name,
                'sport' => $sport
            ]);
        if (!($team instanceof Team)) {
            $team = new Team();
            $team->setName($name);
            $team->setSport($sport);
            $this->manager->persist($team);
        }
        return $team;
    }

    /**
     * Get or insert Source
     *
     * @param string $name
     *
     * @return Source
     */
    public function getOrInsertSource(string $name): Source
    {
        $source = $this->manager
            ->getRepository(Source::class)
            ->findOneBy(['name' => $name]);
        if (!($source instanceof Source)) {
            $source = new Source();
            $source->setName($name);
            $this->manager->persist($source);
        }
        return $source;
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