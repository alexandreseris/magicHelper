<?php

namespace App\Entity;

use App\Repository\FaceManaCostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FaceManaCostRepository::class)
 */
class FaceManaCost
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Face::class, inversedBy="manaCosts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(nullable=false, name="face_id", referencedColumnName="face_id")
     * })
     */
    private $face;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Symbol::class, inversedBy="faceManaCosts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(nullable=false, name="symbol_id", referencedColumnName="code")
     * })
     */
    private $symbol;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getFace(): ?Face
    {
        return $this->face;
    }

    public function setFace(?Face $face): self
    {
        $this->face = $face;

        return $this;
    }

    public function getSymbol(): ?Symbol
    {
        return $this->symbol;
    }

    public function setSymbol(?Symbol $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
