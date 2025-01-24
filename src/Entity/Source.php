<?php

namespace App\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;

/**
 * Source
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(name="search_name_idx", columns={"name"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 */
class Source
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameBuffer", mappedBy="source")
     */
    private $gameBuffers;

    /**
     * Source constructor.
     */
    public function __construct()
    {
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
            $gameBuffer->setSource($this);
        }
        return $this;
    }

    public function removeGameBuffer(GameBuffer $gameBuffer): self
    {
        if ($this->gameBuffers->contains($gameBuffer)) {
            $this->gameBuffers->removeElement($gameBuffer);
            // set the owning side to null (unless already changed)
            if ($gameBuffer->getSource() === $this) {
                $gameBuffer->setSource(null);
            }
        }
        return $this;
    }
}