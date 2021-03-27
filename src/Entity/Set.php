<?php

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SetRepository::class)
 * @ORM\Table(name="setOfCard")
 */
class Set
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $released_date;

    /**
     * @ORM\ManyToOne(targetEntity=SetType::class, inversedBy="sets")
     * @ORM\JoinColumn(nullable=false, name="setType_id", referencedColumnName="code")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Card::class, mappedBy="set")
     */
    private $cards;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon_url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon_local;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getReleasedDate(): ?\DateTimeInterface
    {
        return $this->released_date;
    }

    public function setReleasedDate(?\DateTimeInterface $released_date): self
    {
        $this->released_date = $released_date;

        return $this;
    }

    public function getType(): ?SetType
    {
        return $this->type;
    }

    public function setType(?SetType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setSet($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getSet() === $this) {
                $card->setSet(null);
            }
        }

        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    public function setIconUrl(string $icon_url): self
    {
        $this->icon_url = $icon_url;

        return $this;
    }

    public function getIconLocal(): ?string
    {
        return $this->icon_local;
    }

    public function setIconLocal(?string $icon_local): self
    {
        $this->icon_local = $icon_local;

        return $this;
    }
}
