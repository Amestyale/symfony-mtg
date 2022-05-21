<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Classe\Color;
use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['card']],
        'denormalization_context' => ['groups' => ['card']],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    static $colors = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["card"])]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 200,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'string', length: 200)]
    #[Groups(["card"])]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 50,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[Assert\Regex(
        pattern: '/^(\{[^\{\}]*\})*$/i',
        htmlPattern: '^(\{[^\{\}]*\})*$',
        message: "The mana cost need to respect pattern "
    )]
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(["card"])]
    private $cost;


    #[Assert\Length(
        max: 1000,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: Edition::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["card"])]
    private $edition;

    #[Assert\NotBlank]
    #[Assert\Url]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The url cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["card"])]
    private $image;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['mythic', 'rare', 'uncommon', 'common'], message: 'Choose a valid rarity')]
    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private $rarity;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: CardDeck::class, orphanRemoval: true)]
    private $cardDecks;

    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->cardDecks = new ArrayCollection();
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

    public function getCost(): ?string
    {
        return $this->cost;
    }

    #[Groups(["card"])]
    public function getImagedCost()
    {
        return array_map(function($el){
            $colors = new Color($el);
            return $colors->getSymbol();
        },preg_split('/(?<=\})(?<!\{)(?=.)/', $this->getCost()));
        
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

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

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(?string $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * @return Collection<int, CardDeck>
     */
    public function getCardDecks(): Collection
    {
        return $this->cardDecks;
    }

    public function addCardDeck(CardDeck $cardDeck): self
    {
        if (!$this->cardDecks->contains($cardDeck)) {
            $this->cardDecks[] = $cardDeck;
            $cardDeck->setCard($this);
        }

        return $this;
    }

    public function removeCardDeck(CardDeck $cardDeck): self
    {
        if ($this->cardDecks->removeElement($cardDeck)) {
            // set the owning side to null (unless already changed)
            if ($cardDeck->getCard() === $this) {
                $cardDeck->setCard(null);
            }
        }

        return $this;
    }
}
