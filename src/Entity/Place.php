<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(type:"decimal", length:9, scale:6)]
    private ?float $latitude = null;

    #[ORM\Column(type:"decimal", length:9, scale:6)]
    private ?float $longitude = null;



    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'place')]
    private Collection $sortie;

    #[ORM\Column(length: 30)]
    private ?string $cityName = null;

    #[ORM\Column(length: 5)]
    private ?string $postalCode = null;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
    }

    public function __toString()
    {
        $sortie = $this->getSortie()->first();
        $sortieString = $sortie ? sprintf(' - %s', $sortie->getName()) : '';

        return sprintf(
            '%s - %s, %s (%s, %s)%s',
            $this->getName(),
            $this->getStreet(),
            $this->getCity(),
            $this->getLatitude(),
            $this->getLongitude(),
            $sortieString
        );
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }


    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function setSortie(Collection $sortie): void
    {
        $this->sortie = $sortie;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setPlace($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getPlace() === $this) {
                $sortie->setPlace(null);
            }
        }

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): static
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }
}
