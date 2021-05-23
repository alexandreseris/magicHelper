<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     */
    private $id_scryfall;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $id_oracle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_arena;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $released_date;

    /**
     * @ORM\ManyToOne(targetEntity=Layout::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false, name="layout_id", referencedColumnName="code")
     */
    private $layout;

    /**
     * @ORM\ManyToOne(targetEntity=Rarity::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false, name="rarity_id", referencedColumnName="name")
     */
    private $rarity;

    /**
     * @ORM\ManyToOne(targetEntity=Set::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false, name="set_id", referencedColumnName="code")
     */
    private $set;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class)
     * @ORM\JoinTable(name="card_colorIdentity",
     *   joinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id_scryfall")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="color_id", referencedColumnName="code")}
     * )
     */
    private $color_identity;

    /**
     * @ORM\ManyToMany(targetEntity=Symbol::class)
     * @ORM\JoinTable(name="card_producedMana",
     *   joinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id_scryfall")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="symbol_id", referencedColumnName="code")}
     * )
     */
    private $produced_mana;

    /**
     * @ORM\ManyToMany(targetEntity=Keyword::class, inversedBy="cards")
     * @ORM\JoinTable(name="card_keyword",
     *   joinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id_scryfall")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="name")}
     * )
     */
    private $keywords;

    /**
     * @ORM\ManyToMany(targetEntity=Card::class)
     * @ORM\JoinTable(name="card_related",
     *   joinColumns={@ORM\JoinColumn(name="card_id", referencedColumnName="id_scryfall")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="relatedCard_id", referencedColumnName="id_scryfall")}
     * )
     */
    private $related;

    /**
     * @ORM\OneToMany(targetEntity=Face::class, mappedBy="card")
     */
    private $faces;

    /**
     * @ORM\OneToMany(targetEntity=CardLegality::class, mappedBy="card")
     */
    private $legalities;

    public function __construct()
    {
        $this->color_identity = new ArrayCollection();
        $this->produced_mana = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        $this->related = new ArrayCollection();
        $this->faces = new ArrayCollection();
        $this->legalities = new ArrayCollection();
    }

    public function getIdScryfall(): ?string
    {
        return $this->id_scryfall;
    }

    public function setIdScryfall(string $id_scryfall): self
    {
        $this->id_scryfall = $id_scryfall;

        return $this;
    }

    public function getIdOracle(): ?string
    {
        return $this->id_oracle;
    }

    public function setIdOracle(string $id_oracle): self
    {
        $this->id_oracle = $id_oracle;

        return $this;
    }

    public function getIdArena(): ?int
    {
        return $this->id_arena;
    }

    public function setIdArena(?int $id_arena): self
    {
        $this->id_arena = $id_arena;

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

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    public function setLayout(?Layout $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function getRarity(): ?Rarity
    {
        return $this->rarity;
    }

    public function setRarity(?Rarity $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getSet(): ?Set
    {
        return $this->set;
    }

    public function setSet(?Set $set): self
    {
        $this->set = $set;

        return $this;
    }

    /**
     * @return Collection|Color[]
     */
    public function getColorIdentity(): Collection
    {
        return $this->color_identity;
    }

    public function addColorIdentity(Color $colorIdentity): self
    {
        if (!$this->color_identity->contains($colorIdentity)) {
            $this->color_identity[] = $colorIdentity;
        }

        return $this;
    }

    public function removeColorIdentity(Color $colorIdentity): self
    {
        $this->color_identity->removeElement($colorIdentity);

        return $this;
    }

    /**
     * @return Collection|Symbol[]
     */
    public function getProducedMana(): Collection
    {
        return $this->produced_mana;
    }

    public function addProducedMana(Symbol $producedMana): self
    {
        if (!$this->produced_mana->contains($producedMana)) {
            $this->produced_mana[] = $producedMana;
        }

        return $this;
    }

    public function removeProducedMana(Symbol $producedMana): self
    {
        $this->produced_mana->removeElement($producedMana);

        return $this;
    }

    /**
     * @return Collection|Keyword[]
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): self
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getRelated(): Collection
    {
        return $this->related;
    }

    public function addRelated(self $related): self
    {
        if (!$this->related->contains($related)) {
            $this->related[] = $related;
        }

        return $this;
    }

    public function removeRelated(self $related): self
    {
        $this->related->removeElement($related);

        return $this;
    }

    /**
     * @return Collection|Face[]
     */
    public function getFaces(): Collection
    {
        return $this->faces;
    }

    public function addFace(Face $face): self
    {
        if (!$this->faces->contains($face)) {
            $this->faces[] = $face;
            $face->setCard($this);
        }

        return $this;
    }

    public function removeFace(Face $face): self
    {
        if ($this->faces->removeElement($face)) {
            // set the owning side to null (unless already changed)
            if ($face->getCard() === $this) {
                $face->setCard(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CardLegality[]
     */
    public function getLegalities(): Collection
    {
        return $this->legalities;
    }

    public function addLegality(CardLegality $legality): self
    {
        if (!$this->legalities->contains($legality)) {
            $this->legalities[] = $legality;
            $legality->setCard($this);
        }

        return $this;
    }

    public function removeLegality(CardLegality $legality): self
    {
        if ($this->legalities->removeElement($legality)) {
            // set the owning side to null (unless already changed)
            if ($legality->getCard() === $this) {
                $legality->setCard(null);
            }
        }

        return $this;
    }

    // virtual column
    public function getName(): string
    {
        $facesName = [];
        foreach ($this->getFaces() as $face) {
            $facesName[] = $face->getName();
        }
        return implode(" - ", $facesName);
    }

}
