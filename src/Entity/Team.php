<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="search_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sport", inversedBy="teams")
     */
    private $sport;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="team1")
     */
    private $games1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="team2")
     */
    private $games2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameBuffer", mappedBy="team1")
     */
    private $gameBuffers1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameBuffer", mappedBy="team2")
     */
    private $gameBuffers2;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->games1 = new ArrayCollection();
        $this->games2 = new ArrayCollection();
        $this->gameBuffers1 = new ArrayCollection();
        $this->gameBuffers2 = new ArrayCollection();
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
    public function getGames1(): Collection
    {
        return $this->games1;
    }

    public function addGame1(Game $game): self
    {
        if (!$this->games1->contains($game)) {
            $this->games1[] = $game;
            $game->setTeam1($this);
        }
        return $this;
    }

    public function removeGame1(Game $game): self
    {
        if ($this->games1->contains($game)) {
            $this->games1->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getTeam1() === $this) {
                $game->setTeam1(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames2(): Collection
    {
        return $this->games2;
    }

    public function addGames2(Game $games2): self
    {
        if (!$this->games2->contains($games2)) {
            $this->games2[] = $games2;
            $games2->setTeam2($this);
        }
        return $this;
    }

    public function removeGames2(Game $games2): self
    {
        if ($this->games2->contains($games2)) {
            $this->games2->removeElement($games2);
            // set the owning side to null (unless already changed)
            if ($games2->getTeam2() === $this) {
                $games2->setTeam2(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|GameBuffer[]
     */
    public function getGameBuffers1(): Collection
    {
        return $this->gameBuffers1;
    }

    public function addGameBuffer1(GameBuffer $gameBuffer): self
    {
        if (!$this->gameBuffers1->contains($gameBuffer)) {
            $this->gameBuffers1[] = $gameBuffer;
            $gameBuffer->setTeam1($this);
        }
        return $this;
    }

    public function removeGameBuffer1(GameBuffer $gameBuffer): self
    {
        if ($this->gameBuffers1->contains($gameBuffer)) {
            $this->gameBuffers1->removeElement($gameBuffer);
            // set the owning side to null (unless already changed)
            if ($gameBuffer->getTeam1() === $this) {
                $gameBuffer->setTeam1(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|GameBuffer[]
     */
    public function getGameBuffers2(): Collection
    {
        return $this->gameBuffers2;
    }

    public function addGameBuffer2(GameBuffer $gameBuffer): self
    {
        if (!$this->gameBuffers2->contains($gameBuffer)) {
            $this->gameBuffers2[] = $gameBuffer;
            $gameBuffer->setTeam2($this);
        }
        return $this;
    }

    public function removeGameBuffer2(GameBuffer $gameBuffer): self
    {
        if ($this->gameBuffers2->contains($gameBuffer)) {
            $this->gameBuffers2->removeElement($gameBuffer);
            // set the owning side to null (unless already changed)
            if ($gameBuffer->getTeam2() === $this) {
                $gameBuffer->setTeam2(null);
            }
        }
        return $this;
    }
}