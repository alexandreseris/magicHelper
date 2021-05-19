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
    private $is_ability;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_action;

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
        return $this->is_ability;
    }

    public function setIsAbility(bool $is_ability): self
    {
        $this->is_ability = $is_ability;

        return $this;
    }

    public function getIsAction(): ?bool
    {
        return $this->is_action;
    }

    public function setIsAction(bool $is_action): self
    {
        $this->is_action = $is_action;

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
