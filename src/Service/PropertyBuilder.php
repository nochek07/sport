<?php

namespace App\Service;

use App\DTO\GameBufferDTO;
use App\Entity as Entity;
use App\Utils\Property\{PropLanguage, PropLeague, PropSource, PropSport, PropTeam1, PropTeam2};
use Doctrine\ORM\EntityManagerInterface;

class PropertyBuilder
{
    private PropLanguage $propertyLanguage;

    private PropSport $propertySport;

    private PropLeague $propertyLeague;

    private PropTeam1 $propertyTeam1;

    private PropTeam2 $propertyTeam2;

    private PropSource $propertySource;

    private EntityManagerInterface $manager;

    /**
     * PropertyBuilder constructor.
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
    public function fillingData(array $dtoArray): void
    {
        $this->propertyLanguage = new PropLanguage($this->manager);
        $this->propertySport = new PropSport($this->manager);
        $this->propertyLeague = new PropLeague($this->manager);
        $this->propertyTeam1 = new PropTeam1($this->manager);
        $this->propertyTeam2 = new PropTeam2($this->manager);
        $this->propertySource = new PropSource($this->manager);

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

    public function getDataFilter(GameBufferDTO $dto): array
    {
        $lang = $this->propertyLanguage->lookForOutData($dto);
        if (!($lang instanceof Entity\Language)) {
            $lang = $this->propertyLanguage->insert($dto);
            $this->propertyLanguage->addOutData([$lang]);
        }

        $source = $this->propertySource->lookForOutData($dto);
        if (!($source instanceof Entity\Source)) {
            $source = $this->propertySource->insert($dto);
            $this->propertySource->addOutData([$source]);
        }

        $sport = $this->propertySport->lookForOutData($dto);
        if (!($sport instanceof Entity\Sport)) {
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
            if (!($league instanceof Entity\League)) {
                $league = $this->propertyLeague->insert($dto, $sport);
                $this->propertyLeague->addOutData([$league]);
            }

            $team1 = $this->propertyTeam1->lookForOutData($dto, $sport);
            if (!($team1 instanceof Entity\Team)) {
                $team1 = $this->propertyTeam1->insert($dto, $sport);
                $this->propertyTeam1->addOutData([$team1]);
                $this->propertyTeam2->addOutData([$team1]);
            }

            $team2 = $this->propertyTeam2->lookForOutData($dto, $sport);
            if (!($team2 instanceof Entity\Team)) {
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