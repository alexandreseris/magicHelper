<?php

namespace App\Entity;

use App\Repository\CardLegalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CardLegalityRepository::class)
 */
class CardLegality
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Card::class, inversedBy="legalities")
     * @ORM\JoinColumn(nullable=false, name="card_id", referencedColumnName="id_scryfall")
     */
    private $card;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Format::class)
     * @ORM\JoinColumn(nullable=false, name="format_id", referencedColumnName="code")
     */
    private $format;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Legality::class)
     * @ORM\JoinColumn(nullable=false, name="legality_id", referencedColumnName="code")
     */
    private $legality;


    public function __construct()
    {}

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getLegality(): ?Legality
    {
        return $this->legality;
    }

    public function setLegality(?Legality $legality): self
    {
        $this->legality = $legality;

        return $this;
    }

}
