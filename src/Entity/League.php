<?php

namespace App\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

/**
 * League
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="search_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\LeagueRepository")
 */
class League
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sport", inversedBy="leagues")
     */
    private $sport;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="league")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameBuffer", mappedBy="league")
     */
    private $gameBuffers;

    /**
     * League constructor.
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->gameBuffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSport(): ?Sport
    {
        return $this->sport;
    }

    public function setSport(?Sport $sport): self
    {
        $this->sport = $sport;
        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setLeague($this);
        }
        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getLeague() === $this) {
                $game->setLeague(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|GameBuffer[]
     */
    public function getGameBuffers(): Collection
    {
        return $this->gameBuffers;
    }

    public function addGameBuffer(GameBuffer $gameBuffer): self
    {
        if (!$this->gameBuffers->contains($gameBuffer)) {
            $this->gameBuffers[] = $gameBuffer;
            $gameBuffer->setLeague($this);
        }
        return $this;
    }

    public function removeGameBuffer(GameBuffer $gameBuffer): self
    {
        if ($this->gameBuffers->contains($gameBuffer)) {
            $this->gameBuffers->removeElement($gameBuffer);
            // set the owning side to null (unless already changed)
            if ($gameBuffer->getLeague() === $this) {
                $gameBuffer->setLeague(null);
            }
        }

        return $this;
    }
}