<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Repository\{GameBufferRepository, GameRepository};
use Doctrine\ORM\NonUniqueResultException;
use App\Entity\{Game, GameBuffer};
use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiV1
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 0;

    private EntityManagerInterface $manager;

    private PropertyBuilder $propertyBuilder;

    private BackgroundCommander $process;

    private ValidatorInterface $validator;

    private SerializerInterface $serializer;

    /**
     * ApiV1 constructor.
     */
    public function __construct(
        EntityManagerInterface $manager,
        PropertyBuilder $propertyBuilder,
        BackgroundCommander $process,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->manager = $manager;
        $this->propertyBuilder = $propertyBuilder;
        $this->process = $process;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Add Game
     */
    public function addGameByJson(string $context): array
    {
        $data = $this->getDeserializedData($context, $this->serializer);
        if (isset($data['events'])) {
            $validatedData = $this->getValidatedDTO($data['events'], $this->validator);
            if (sizeof($validatedData) > 0) {
                $this->propertyBuilder->fillingData($validatedData);

                $newGames = [];
                foreach ($validatedData as $dto) {
                    $filter = $this->propertyBuilder->getDataFilter($dto);

                    // Game_Buffer
                    $gameBuffer = $this->manager
                        ->getRepository(GameBuffer::class)
                        ->findOneBy($filter);
                    if (!($gameBuffer instanceof GameBuffer)) {
                        $gameBuffer = new GameBuffer();
                        $gameBuffer
                            ->setLeague($filter['league'])
                            ->setLanguage($filter['language'])
                            ->setTeam1($filter['team1'])
                            ->setTeam2($filter['team2'])
                            ->setDate($filter['date'])
                            ->setSource($filter['source']);
                        $this->manager->persist($gameBuffer);

                        $newGames[] = $gameBuffer;
                    }
                }
                if (sizeof($newGames) > 0) {
                    $this->manager->flush();
                    $this->process->runProcess($newGames);
                }
                return [
                    'success' => self::RESULT_SUCCESS
                ];
            }
        }
        return [
            'success' => self::RESULT_FAIL
        ];
    }

    /**
     * Get random game
     * Get random game by filter
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function random(array $query): array
    {
        /**
         * @var GameRepository $gameRepository
         */
        $gameRepository = $this->manager
            ->getRepository(Game::class);
        $randGame = $gameRepository->getRandom();

        $result = [];
        if ($randGame instanceof Game) {
            $filter = $this->getFilterFromRequest($query);

            /**
             * @var GameBufferRepository $gameBufferRepository
             */
            $gameBufferRepository = $this->manager
                ->getRepository(GameBuffer::class);
            $gamesBuffer = $gameBufferRepository->findByGame($randGame, $filter);

            $result = [
                "game" => $randGame,
                "buffers" => $gamesBuffer
            ];
        }
        return $result;
    }

    /**
     * Add games by array for command
     * @throws NonUniqueResultException
     */
    public function addGamesByArray(array $games): void
    {
        /**
         * @var GameBuffer[] $foundGameBuffers
         */
        $foundGameBuffers = $this->manager
            ->getRepository(GameBuffer::class)
            ->findBy(['id' => $games]);

        foreach ($foundGameBuffers as $gameBuffer) {
            $sport = $gameBuffer->getLeague()->getSport();
            $diff = $sport->getDiff();
            /**
             * @var \DateTimeImmutable $date
             */
            $date = $gameBuffer->getDate();

            $dateStart = $date->modify("- $diff hour");
            $dateEnd = $date->modify("+ $diff hour");

            /**
             * @var GameRepository $gameRepository
             */
            $gameRepository = $this->manager
                ->getRepository(Game::class);
            $foundGame = $gameRepository->findByBuffer($gameBuffer, $dateStart, $dateEnd);
            if ($foundGame instanceof Game) {
                $difference = $date->diff($foundGame->getDate());
                if ($difference->invert == 1) {
                    $foundGame->setDate($date);
                }
                $gameBuffer->setGame($foundGame);
            } else {
                $game = new Game();
                $game->setLeague($gameBuffer->getLeague());
                $game->setLanguage($gameBuffer->getLanguage());
                $game->setTeam1($gameBuffer->getTeam1());
                $game->setTeam2($gameBuffer->getTeam2());
                $game->setDate($gameBuffer->getDate());
                $this->manager->persist($game);
                $gameBuffer->setGame($game);
            }
            $this->manager->flush();
        }
    }

    /**
     * Get deserialized data
     *
     * @return GameBufferDTO[][]
     */
    private function getDeserializedData(string $context, SerializerInterface $serializer): array
    {
        try {
            $data = $serializer->deserialize($context, 'App\DTO\GameBufferDTO[][]', JsonEncoder::FORMAT);
        } catch (UnexpectedValueException $e) {
            $data = [];
        }
        return $data;
    }

    /**
     * Get Validated DTO Game Buffer
     *
     * @return GameBufferDTO[]
     */
    private function getValidatedDTO(array $events, ValidatorInterface $validator): array
    {
        $result = [];
        foreach ($events as $event) {
            $errors = $validator->validate($event);
            if (count($errors) == 0) {
                $result[] = $event;
            }
        }

        return $result;
    }

    /**
     * Get Filter array from query
     */
    private function getFilterFromRequest(array $query): array
    {
        $filter = [];
        $source = trim($query['source'] ?? '');
        if (!empty($source)) {
            $filter['source'] = $source;
        }
        if (Util::isDate($query['start'] ?? '')
            && Util::isDate($query['end'] ?? '')) {
            $filter['start'] = $query['start'];
            $filter['end'] = $query['end'];
        }
        return $filter;
    }
}