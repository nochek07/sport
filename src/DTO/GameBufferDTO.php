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
        $this->setLeague($params['league'] ?? '');
        $this->setTeam1($params['team1'] ?? '');
        $this->setTeam2($params['team2'] ?? '');
        $this->setLeague($params['league'] ?? '');
        $this->setLanguage($params['lang'] ?? '');
        $this->setSource($params['source'] ?? '');

        try {
            $date = new \DateTime($params['date'] ?? '-');
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