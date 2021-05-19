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
     * @ORM\Column(type="string", length=40)
     */
    private $face_id;

    /**
     * @ORM\ManyToOne(targetEntity=Card::class, inversedBy="faces")
     * @ORM\JoinColumn(nullable=false, name="card_id", referencedColumnName="id_scryfall")
     */
    private $card;

    /**
     * @ORM\Column(type="integer")
     */
    private $face_index;

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
    private $power_value;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $toughness_value;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class)
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="name")
     */
    private $artist;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class)
     * @ORM\JoinTable(name="face_color",
     *   joinColumns={
     *     @ORM\JoinColumn(name="face_id", referencedColumnName="face_id"),
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="color_id", referencedColumnName="code")
     *   }
     * )
     */
    private $colors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_local;

    /**
     * @ORM\OneToMany(targetEntity=FaceManaCost::class, mappedBy="face")
     */
    private $mana_costs;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->mana_costs = new ArrayCollection();
    }

    public function getFaceId(): ?string
    {
        return $this->face_id;
    }

    public function setFaceId(string $face_id): self
    {
        $this->face_id = $face_id;

        return $this;
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

    public function getFaceIndex(): ?int
    {
        return $this->face_index;
    }

    public function setFaceIndex(int $face_index): self
    {
        $this->face_index = $face_index;

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

    public function getPowerValue(): ?string
    {
        return $this->power_value;
    }

    public function setPowerValue(?string $power_value): self
    {
        $this->power_value = $power_value;

        return $this;
    }

    public function getToughnessValue(): ?string
    {
        return $this->toughness_value;
    }

    public function setToughnessValue(?string $toughness_value): self
    {
        $this->toughness_value = $toughness_value;

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

    public function getImageLocal(): ?string
    {
        return $this->image_local;
    }

    public function setImageLocal(?string $image_local): self
    {
        $this->image_local = $image_local;

        return $this;
    }

    /**
     * @return Collection|FaceManaCost[]
     */
    public function getManaCosts(): Collection
    {
        return $this->mana_costs;
    }
}
