<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GameBufferDTO
{
    /**
     * @Assert\NotBlank
     */
    private $league;

    /**
     * @Assert\NotBlank
     */
    private $sport;

    /**
     * @Assert\NotBlank
     */
    private $team1;

    /**
     * @Assert\NotBlank
     */
    private $team2;

    /**
     * @Assert\NotBlank
     * @Assert\Type("\DateTimeInterface")
     */
    private $date;

    /**
     * @Assert\NotBlank
     */
    private $language;

    /**
     * @Assert\NotBlank
     */
    private $source;

    /**
     * GameBufferDTO constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $team1 = $params['team1'] ?? '';
        $team2 = $params['team2'] ?? '';
        $this->setLeague($params['league'] ?? '');
        $this->setSport($params['sport'] ?? '');
        $this->setTeam1($team1);
        if (strcasecmp(trim($team1), trim($team2)) == 0) {
            $team2 = '';
        }
        $this->setTeam2($team2);
        $this->setLeague($params['league'] ?? '');
        $this->setLanguage($params['lang'] ?? '');
        $this->setSource($params['source'] ?? '');

        $dateString = trim($params['date'] ?? '-');
        $dateString = empty($dateString) ? '-' : $dateString;
        try {
            $date = new \DateTime($dateString ?? '-');
            $this->setDate($date);
        } catch (\Exception $e) {
        }
    }


    public function getLeague()
    {
        return $this->league;
    }

    public function setLeague(string $league)
    {
        $this->league = trim($league);

        return $this;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function setSport(string $sport)
    {
        $this->sport = trim($sport);

        return $this;
    }

    public function getTeam1()
    {
        return $this->team1;
    }

    public function setTeam1(string $team1)
    {
        $this->team1 = trim($team1);

        return $this;
    }

    public function getTeam2()
    {
        return $this->team2;
    }

    public function setTeam2(string $team2)
    {
        $this->team2 = trim($team2);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage(string $language)
    {
        $this->language = trim($language);

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource(string $source)
    {
        $this->source = trim($source);

        return $this;
    }
}