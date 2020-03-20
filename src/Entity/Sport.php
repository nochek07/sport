<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sport
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="search_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SportRepository")
 */
class Sport
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
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="sport")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\League", mappedBy="sport")
     */
    private $leagues;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $diff = 0;

    /**
     * Sport constructor.
     */
    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->leagues = new ArrayCollection();
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

    /**
     * @return ArrayCollection|Team[]
     */
    public function getTeams(): ArrayCollection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setSport($this);
        }
        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getSport() === $this) {
                $team->setSport(null);
            }
        }
        return $this;
    }

    /**
     * @return ArrayCollection|League[]
     */
    public function getLeagues(): ArrayCollection
    {
        return $this->leagues;
    }

    public function addLeague(League $league): self
    {
        if (!$this->leagues->contains($league)) {
            $this->leagues[] = $league;
            $league->setSport($this);
        }
        return $this;
    }

    public function removeLeague(League $league): self
    {
        if ($this->leagues->contains($league)) {
            $this->leagues->removeElement($league);
            // set the owning side to null (unless already changed)
            if ($league->getSport() === $this) {
                $league->setSport(null);
            }
        }
        return $this;
    }

    public function getDiff(): ?int
    {
        return $this->diff;
    }

    public function setDiff(?int $diff): self
    {
        $this->diff = $diff;
        return $this;
    }
}