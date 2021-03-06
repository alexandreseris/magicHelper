<?php

namespace App\Entity;

use App\Repository\SymbolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SymbolRepository::class)
 */
class Symbol
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=20)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_funny;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_mana;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon_url;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cmc;

    /**
     * @ORM\ManyToMany(targetEntity=Color::class, inversedBy="symbols")
     * @ORM\JoinTable(name="symbol_color",
     *   joinColumns={@ORM\JoinColumn(name="symbol_id", referencedColumnName="code")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="color_id", referencedColumnName="code")}
     * )
     */
    private $colors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon_local;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $code_variant;

    /**
     * @ORM\OneToMany(targetEntity=FaceManaCost::class, mappedBy="symbol")
     */
    private $face_mana_costs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $index_value;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
        $this->face_mana_costs = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsFunny(): ?bool
    {
        return $this->is_funny;
    }

    public function setIsFunny(bool $is_funny): self
    {
        $this->is_funny = $is_funny;

        return $this;
    }

    public function getIsMana(): ?bool
    {
        return $this->is_mana;
    }

    public function setIsMana(bool $is_mana): self
    {
        $this->is_mana = $is_mana;

        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    public function setIconUrl(?string $icon_url): self
    {
        $this->icon_url = $icon_url;

        return $this;
    }

    public function getCmc(): ?string
    {
        return $this->cmc;
    }

    public function setCmc(?string $cmc): self
    {
        $this->cmc = $cmc;

        return $this;
    }

    public function getIndexValue(): ?int
    {
        return $this->index_value;
    }

    public function setIndexValue(?int $index_value): self
    {
        $this->index_value = $index_value;

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

    public function getIconLocal(): ?string
    {
        return $this->icon_local;
    }

    public function setIconLocal(?string $icon_local): self
    {
        $this->icon_local = $icon_local;

        return $this;
    }

    public function getCodeVariant(): ?string
    {
        return $this->code_variant;
    }

    public function setCodeVariant(?string $code_variant): self
    {
        $this->code_variant = $code_variant;

        return $this;
    }

    /**
     * @return Collection|FaceManaCost[]
     */
    public function getFaceManaCosts(): Collection
    {
        return $this->face_mana_costs;
    }

    public function addFaceManaCost(FaceManaCost $face_mana_costs): self
    {
        if (!$this->face_mana_costs->contains($face_mana_costs)) {
            $this->face_mana_costs[] = $face_mana_costs;
            $face_mana_costs->setSymbol($this);
        }

        return $this;
    }

    public function removeFaceManaCost(FaceManaCost $face_mana_costs): self
    {
        if ($this->face_mana_costs->removeElement($face_mana_costs)) {
            // set the owning side to null (unless already changed)
            if ($face_mana_costs->getSymbol() === $this) {
                $face_mana_costs->setSymbol(null);
            }
        }

        return $this;
    }
}
