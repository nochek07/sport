<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer};
use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiV1
{
    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 0;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var PropertyBuilder
     */
    private $propertyBuilder;

    /**
     * @var BackgroundCommander
     */
    private $process;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     * @param BackgroundCommander $process
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
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
     *
     * @param Request $request
     * @return array
     */
    public function addGameByJson(Request $request): array
    {
        $data = $this->getDeserializedData($request->getContent(), $this->serializer);
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

                return ['success' => self::RESULT_SUCCESS];
            }
        }

        return ['success' => self::RESULT_FAIL];
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
        /**
         * @var Game $randGame
         */
        $randGame = $this->manager
            ->getRepository(Game::class)
            ->getRandom();

        $result = [];
        if ($randGame instanceof Game) {
            $filter = $this->getFilterFromRequest($request);
            $gamesBuffer = $this->manager
                ->getRepository(GameBuffer::class)
                ->findByGame($randGame, $filter);

            $result = [
                "game" => $randGame,
                "buffers" => $gamesBuffer
            ];
        }

        return $result;
    }

    /**
     * Add games by array for command
     *
     * @param array $games
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

    /**
     * Get deserialized data
     *
     * @param string $context
     * @param SerializerInterface $serializer
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
     * @param array $events
     * @param ValidatorInterface $validator
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
     * Get Filter array from Request
     *
     * @param Request $request
     *
     * @return array
     */
    private function getFilterFromRequest(Request $request): array
    {
        $filter = [];
        $source = trim($request->query->get('source') ?? '');
        if (!empty($source)) {
            $filter['source'] = $source;
        }
        if (Util::isDate($request->query->get('start') ?? '')
            && Util::isDate($request->query->get('end') ?? '')) {
            $filter['start'] = $request->query->get('start');
            $filter['end'] = $request->query->get('end');
        }
        return $filter;
    }
}