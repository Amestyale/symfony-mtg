<?php

namespace App\Entity;

use App\Repository\CardDeckRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CardDeckRepository::class)]
class CardDeck
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Deck::class, inversedBy: 'cardDecks')]
    #[ORM\JoinColumn(nullable: false)]
    private $deck;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Card::class, inversedBy: 'cardDecks')]
    #[ORM\JoinColumn(nullable: false)]
    private $card;

    #[ORM\Column(type: 'integer', options : [ "default" => 1] )]
    private $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeck(): ?Deck
    {
        return $this->deck;
    }

    public function setDeck(?Deck $deck): self
    {
        $this->deck = $deck;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function removeOne(): self
    {
        $this->quantity -= 1;

        return $this;
    }
}
