<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

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
     * @Assert\Expression(
     *     "not (this.getTeam1() matches '/'~this.getTeam2()~'/iu')"
     * )
     */
    private $team2;

    /**
     * @Assert\NotBlank
     * @Assert\Type("\DateTimeImmutable")
     */
    private $date;

    /**
     * @Assert\NotBlank
     * @SerializedName("lang")
     */
    private $language;

    /**
     * @Assert\NotBlank
     */
    private $source;

    public function getLeague()
    {
        return $this->league;
    }

    public function setLeague(?string $league): self
    {
        $this->league = trim($league);
        return $this;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function setSport(?string $sport): self
    {
        $this->sport = trim($sport);
        return $this;
    }

    public function getTeam1()
    {
        return $this->team1;
    }

    public function setTeam1(?string $team1): self
    {
        $this->team1 = trim($team1);
        return $this;
    }

    public function getTeam2()
    {
        return $this->team2;
    }

    public function setTeam2(?string $team2): self
    {
        $this->team2 = trim($team2);
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = trim($language);
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = trim($source);
        return $this;
    }
}