<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     * @param KernelInterface $kernel
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $manager,
        PropertyBuilder $propertyBuilder,
        KernelInterface $kernel,
        ValidatorInterface $validator
    ) {
        $this->manager = $manager;
        $this->propertyBuilder = $propertyBuilder;
        $this->kernel = $kernel;
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

                $ArrayForGames = [];
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
}