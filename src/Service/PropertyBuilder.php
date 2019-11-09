<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Utils\Property\{League, Language, Source, Sport, Team1, Team2};
use Doctrine\ORM\EntityManagerInterface;

class PropertyBuilder
{
    /**
     * @var Language
     */
    private $propertyLanguage;

    /**
     * @var Sport
     */
    private $propertySport;

    /**
     * @var League
     */
    private $propertyLeague;

    /**
     * @var Team1
     */
    private $propertyTeam1;

    /**
     * @var Team2
     */
    private $propertyTeam2;

    /**
     * @var Source
     */
    private $propertySource;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * PropertyBuilder constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Filling Out Data
     *
     * @param GameBufferDTO[] $dtoArray
     */
    public function fillingData($dtoArray)
    {
        $this->propertyLanguage = new Language($this->manager);
        $this->propertySport = new Sport($this->manager);
        $this->propertyLeague = new League($this->manager);
        $this->propertyTeam1 = new Team1($this->manager);
        $this->propertyTeam2 = new Team2($this->manager);
        $this->propertySource = new Source($this->manager);

        foreach ($dtoArray as $dto) {
            $this->propertyLanguage->addInData($dto->getLanguage());
            $this->propertySport->addInData($dto->getSport());
            $this->propertyLeague->addInData([$dto->getLeague(), $dto->getSport()]);
            $this->propertyTeam1->addInData([$dto->getTeam1(), $dto->getSport()]);
            $this->propertyTeam1->addInData([$dto->getTeam2(), $dto->getSport()]);
            $this->propertySource->addInData($dto->getSource());
        }

        $this->propertyLanguage->filingOutData();
        $this->propertySport->filingOutData();
        $this->propertyLeague->filingOutData();
        $this->propertyTeam1->filingOutData();
        $this->propertySource->filingOutData();

        $this->propertyTeam2->addOutData($this->propertyTeam1->getOutData());
    }

    public function getFilterData(GameBufferDTO $dto)
    {
        $lang = $this->propertyLanguage->lookForOutData($dto);
        if (!($lang instanceof \App\Entity\Language)) {
            $lang = $this->propertyLanguage->insert($dto);
            $this->propertyLanguage->addOutData([$lang]);
        }

        $source = $this->propertySource->lookForOutData($dto);
        if (!($source instanceof \App\Entity\Source)) {
            $source = $this->propertySource->insert($dto);
            $this->propertySource->addOutData([$source]);
        }

        $sport = $this->propertySport->lookForOutData($dto);
        if (!($sport instanceof \App\Entity\Sport)) {
            $sport = $this->propertySport->insert($dto);
            $this->propertySport->addOutData([$sport]);

            $league = $this->propertyLeague->insert($dto, $sport);
            $this->propertyLeague->addOutData([$league]);

            $team1 = $this->propertyTeam1->insert($dto, $sport);
            $this->propertyTeam1->addOutData([$team1]);
            $this->propertyTeam2->addOutData([$team1]);

            $team2 = $this->propertyTeam2->insert($dto, $sport);
            $this->propertyTeam2->addOutData([$team2]);
            $this->propertyTeam1->addOutData([$team2]);

        } else {
            $league = $this->propertyLeague->lookForOutData($dto, $sport);
            if (!($league instanceof \App\Entity\League)) {
                $league = $this->propertyLeague->insert($dto, $sport);
                $this->propertyLeague->addOutData([$league]);
            }

            $team1 = $this->propertyTeam1->lookForOutData($dto, $sport);
            if (!($team1 instanceof \App\Entity\Team)) {
                $team1 = $this->propertyTeam1->insert($dto, $sport);
                $this->propertyTeam1->addOutData([$team1]);
                $this->propertyTeam2->addOutData([$team1]);
            }

            $team2 = $this->propertyTeam2->lookForOutData($dto, $sport);
            if (!($team2 instanceof \App\Entity\Team)) {
                $team2 = $this->propertyTeam2->insert($dto, $sport);
                $this->propertyTeam2->addOutData([$team2]);
                $this->propertyTeam1->addOutData([$team2]);
            }
        }

        return [
            'language' => $lang,
            'league' => $league,
            'team1' => $team1,
            'team2' => $team2,
            'date' => $dto->getDate(),
            'source' => $source
        ];
    }
}