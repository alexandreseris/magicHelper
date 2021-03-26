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
     * @ORM\JoinColumn(nullable=false, name="card_id", referencedColumnName="idScryfall")
     */
    private $card;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=LegalityType::class)
     * @ORM\JoinColumn(nullable=false, name="legalityType_id", referencedColumnName="name")
     */
    private $legality_type;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=LegalityValue::class)
     * @ORM\JoinColumn(nullable=false, name="legalityValue_id", referencedColumnName="name")
     */
    private $legality_value;


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

    public function getLegalityType(): ?LegalityType
    {
        return $this->legality_type;
    }

    public function setLegalityType(?LegalityType $legality_type): self
    {
        $this->legality_type = $legality_type;

        return $this;
    }

    public function getLegalityValue(): ?LegalityValue
    {
        return $this->legality_value;
    }

    public function setLegalityValue(?LegalityValue $legality_value): self
    {
        $this->legality_value = $legality_value;

        return $this;
    }

}
