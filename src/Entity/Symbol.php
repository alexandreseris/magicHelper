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
    private $isFunny;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMana;

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

    public function __construct()
    {
        $this->colors = new ArrayCollection();
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
        return $this->isFunny;
    }

    public function setIsFunny(bool $isFunny): self
    {
        $this->isFunny = $isFunny;

        return $this;
    }

    public function getIsMana(): ?bool
    {
        return $this->isMana;
    }

    public function setIsMana(bool $isMana): self
    {
        $this->isMana = $isMana;

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
}
