<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var BackgroundProcess
     */
    private $process;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     * @param BackgroundProcess $process
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $manager,
        PropertyBuilder $propertyBuilder,
        BackgroundProcess $process,
        ValidatorInterface $validator
    ) {
        $this->manager = $manager;
        $this->propertyBuilder = $propertyBuilder;
        $this->process = $process;
        $this->validator = $validator;
    }

    /**
     * Add Game
     *
     * @param Request $request
     *
     * @return array
     */
    public function addGame(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['events'])) {
            $dtoArray = $this->fillingAndValidateDTO($data['events']);
            if (sizeof($dtoArray) > 0) {
                $this->propertyBuilder->fillingData($dtoArray);

                $arrayForGames = [];
                foreach ($dtoArray as $dto) {
                    $filter = $this->propertyBuilder->getFilterData($dto);

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

                        $arrayForGames[] = $gameBuffer;
                    }
                }

                if (sizeof($arrayForGames) > 0) {
                    $this->manager->flush();
                    $arrayForGamesID = [];
                    foreach ($arrayForGames as $gameBuffer) {
                        $arrayForGamesID[] = $gameBuffer->getId();
                    }
                    $this->process->runProcess($arrayForGames);
                }

                return ['success' => 1];
            }
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
            $source = $request->query->get('source');
            if (!is_null($source) && !empty($source)) {
                $filter['source'] = $request->query->get('source');
            }
            $start = $request->query->get('start');
            $end = $request->query->get('end');
            if (!is_null($start) && !is_null($end) && $this->isDate($start) && $this->isDate($end)) {
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
     * Filling And Validate DTO Game Buffer
     *
     * @param array $events
     *
     * @return GameBufferDTO[]
     */
    private function fillingAndValidateDTO(array $events)
    {
        $result = [];
        foreach ($events as $event) {
            $dto = new GameBufferDTO($event);
            $errors = $this->validator->validate($dto);
            if (count($errors) == 0) {
                $result[] = $dto;
            }
        }

        return $result;
    }

    /**
     * Check string for date
     *
     * @param string $str
     * @return bool
     */
    private function isDate(string $str)
    {
        return empty($str) ? false : is_numeric(strtotime($str));
    }
}