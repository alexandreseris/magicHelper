<?php

namespace App\Entity;

use App\Repository\FaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FaceRepository::class)
 */
class Face
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Card::class, inversedBy="faces")
     * @ORM\JoinColumn(nullable=false, name="card_id", referencedColumnName="idScryfall")
     */
    private $card;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $index;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type_line;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $oracle_text;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $printed_text;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $power;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $toughness;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class)
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="name")
     */
    private $artist;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class)
     * @ORM\JoinTable(name="face_color",
     *   joinColumns={
     *     @ORM\JoinColumn(name="card_id", referencedColumnName="card_id"),
     *     @ORM\JoinColumn(name="face_index", referencedColumnName="index")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="color_id", referencedColumnName="code")
     *   }
     * )
     */
    private $colors;

    /**
     * @ORM\ManyToMany(targetEntity=Symbol::class)
     * @ORM\JoinTable(name="face_manaCost",
     *   joinColumns={
     *     @ORM\JoinColumn(name="card_id", referencedColumnName="card_id"),
     *     @ORM\JoinColumn(name="face_index", referencedColumnName="index")
     *   },
     *   inverseJoinColumns={@ORM\JoinColumn(name="symbol_id", referencedColumnName="code")}
     * )
     */
    private $mana_costs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_local;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->mana_costs = new ArrayCollection();
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
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

    public function getTypeLine(): ?string
    {
        return $this->type_line;
    }

    public function setTypeLine(string $type_line): self
    {
        $this->type_line = $type_line;

        return $this;
    }

    public function getOracleText(): ?string
    {
        return $this->oracle_text;
    }

    public function setOracleText(?string $oracle_text): self
    {
        $this->oracle_text = $oracle_text;

        return $this;
    }

    public function getPrintedText(): ?string
    {
        return $this->printed_text;
    }

    public function setPrintedText(?string $printed_text): self
    {
        $this->printed_text = $printed_text;

        return $this;
    }

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(?string $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getToughness(): ?string
    {
        return $this->toughness;
    }

    public function setToughness(?string $toughness): self
    {
        $this->toughness = $toughness;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * @return Collection|Color[]
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): self
    {
        if (!$this->colors->contains($color)) {
            $this->colors[] = $color;
        }

        return $this;
    }

    public function removeColor(Color $color): self
    {
        $this->colors->removeElement($color);

        return $this;
    }

    /**
     * @return Collection|Symbol[]
     */
    public function getManaCosts(): Collection
    {
        return $this->mana_costs;
    }

    public function addManaCost(Symbol $manaCost): self
    {
        if (!$this->mana_costs->contains($manaCost)) {
            $this->mana_costs[] = $manaCost;
        }

        return $this;
    }

    public function removeManaCost(Symbol $manaCost): self
    {
        $this->mana_costs->removeElement($manaCost);

        return $this;
    }

    public function getImageLocal(): ?string
    {
        return $this->image_local;
    }

    public function setImageLocal(?string $image_local): self
    {
        $this->image_local = $image_local;

        return $this;
    }
}
