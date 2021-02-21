<?php

namespace App\Entity;

use App\Repository\ColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ColorRepository::class)
 */
class Color
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=1)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Symbol::class, mappedBy="color_id")
     * @ORM\JoinTable(name="Symbol_Color",
     *   joinColumns={@ORM\JoinColumn(name="color_id", referencedColumnName="code")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="symbol_id", referencedColumnName="code")}
     * )
     */
    private $symbols;

    public function __construct()
    {
        $this->symbols = new ArrayCollection();
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

    /**
     * @return Collection|Symbol[]
     */
    public function getSymbols(): Collection
    {
        return $this->symbols;
    }

    public function addSymbol(Symbol $symbol): self
    {
        if (!$this->symbols->contains($symbol)) {
            $this->symbols[] = $symbol;
            $symbol->addColor($this);
        }

        return $this;
    }

    public function removeSymbol(Symbol $symbol): self
    {
        if ($this->symbols->removeElement($symbol)) {
            $symbol->removeColor($this);
        }

        return $this;
    }
}
