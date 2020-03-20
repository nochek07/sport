<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameBuffer
 *
 * @ORM\Table(
 *     name="game_buffer",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="search_idx",
 *              columns={"league_id", "team1_id", "team2_id", "date", "language_id", "source_id"}
 *          )
 *     },
 *     indexes={
 *          @ORM\Index(name="search_date_idx", columns={"date"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\GameBufferRepository")
 */
class GameBuffer implements GameInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\League", inversedBy="gameBuffers")
     */
    private $league;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="gameBuffers1")
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="gameBuffers2")
     */
    private $team2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="gameBuffers")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="gameBuffers")
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="gameBuffers")
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function getTeam1(): ?Team
    {
        return $this->team1;
    }

    public function setTeam1(?Team $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?Team
    {
        return $this->team2;
    }

    public function setTeam2(?Team $team2): self
    {
        $this->team2 = $team2;
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

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;
        return $this;
    }
}