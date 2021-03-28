<?php

namespace App\Entity;

use App\Repository\KeywordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KeywordRepository::class)
 */
class Keyword
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAbility;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAction;

    /**
     * @ORM\ManyToMany(targetEntity=Card::class, mappedBy="keywords")
     * @ORM\JoinTable(name="card_keyword",
     *   joinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="name")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id_scryfall")}
     * )
     */
    private $cards;

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

    public function getIsAbility(): ?bool
    {
        return $this->isAbility;
    }

    public function setIsAbility(bool $isAbility): self
    {
        $this->isAbility = $isAbility;

        return $this;
    }

    public function getIsAction(): ?bool
    {
        return $this->isAction;
    }

    public function setIsAction(bool $isAction): self
    {
        $this->isAction = $isAction;

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
            $card->addKeyword($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            $card->removeKeyword($this);
        }

        return $this;
    }
}
