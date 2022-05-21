<?php

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeckRepository::class)]
class Deck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[Assert\Length(
        max: 1000,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'deck', targetEntity: CardDeck::class, orphanRemoval: true)]
    private $cardDecks;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->card = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, CardDeck>
     */
    public function getCardDeck(): Collection
    {
        return $this->cardDecks;
    }

    public function getCards(): Array
    {
        return array_map(function(CardDeck $cardDecks){
            $card = $cardDecks->getCard();
            $card->quantity = $cardDecks->getQuantity();
            return $card;
        }, $this->cardDecks->toArray());
    }
}
