<?php

namespace App\Entity;

use App\Repository\EditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EditionRepository::class)]
class Edition
{
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 10,
        maxMessage: 'The code cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 150,
        maxMessage: 'The name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'string', length: 150)]
    #[Groups(["card"])]
    private $name;

    #[ORM\OneToMany(mappedBy: 'edition', targetEntity: Card::class, orphanRemoval: true)]
    private $cards;

    #[Assert\NotBlank]
    #[Assert\Range(
        min: '1993-01-01',
        max: 'last day of December +2 years',
        notInRangeMessage: 'The year of the date must be within 1993 and the current year plus two'
    )]
    #[ORM\Column(type: 'date')]
    private $date;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/http(s)?:\/\/.*\.svg(\?.*)*$/i',
        htmlPattern: 'http(s)?:\/\/.*\.svg(\?.*)*$',
        message: "The icon must be an absolute url pointing to an svg"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The url cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["card"])]
    private $icon;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId($id): self
    {
        $this->id = $id;

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
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setEdition($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getEdition() === $this) {
                $card->setEdition(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
