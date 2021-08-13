<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Entity\{Game, GameBuffer};
use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * ApiV1 constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PropertyBuilder $propertyBuilder
     * @param BackgroundCommander $process
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $manager,
        PropertyBuilder $propertyBuilder,
        BackgroundCommander $process,
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
            $dtoArray = $this->fillingAndValidateDTO($data['events'], $this->validator);
            if (sizeof($dtoArray) > 0) {
                $this->propertyBuilder->fillingData($dtoArray);

                $newGames = [];
                foreach ($dtoArray as $dto) {
                    $filter = $this->propertyBuilder->getFilterData($dto);

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
     * Filling And Validate DTO Game Buffer
     *
     * @param array $events
     * @param ValidatorInterface $validator
     *
     * @return GameBufferDTO[]
     */
    private function fillingAndValidateDTO(array $events, ValidatorInterface $validator): array
    {
        $result = [];
        foreach ($events as $event) {
            $dto = new GameBufferDTO($event);
            $errors = $validator->validate($dto);
            if (count($errors) == 0) {
                $result[] = $dto;
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