<?php

namespace App\Entity;

use App\Repository\SearchDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchDataRepository::class)]
class SearchData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(nullable: true)]
    private ?bool $choiseMeOrganisator = null;

    #[ORM\Column(nullable: true)]
    private ?bool $choiseMeInscribed = null;

    #[ORM\Column(nullable: true)]
    private ?bool $choiseMeNotInscribed = null;

    #[ORM\Column(nullable: true)]
    private ?bool $finishedEvents = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $search = null;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    private ?Site $site = null;

    #[ORM\Column(nullable: true)]
    private ?int $userId = null;


//    public function __construct()
//    {
//        $this->sites = new Site();
//    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function isChoiseMeOrganisator(): ?bool
    {
        return $this->choiseMeOrganisator;
    }

    public function setChoiseMeOrganisator(?bool $choiseMeOrganisator): static
    {
        $this->choiseMeOrganisator = $choiseMeOrganisator;

        return $this;
    }

    public function isChoiseMeInscribed(): ?bool
    {
        return $this->choiseMeInscribed;
    }

    public function setChoiseMeInscribed(?bool $choiseMeInscribed): static
    {
        $this->choiseMeInscribed = $choiseMeInscribed;

        return $this;
    }

    public function isChoiseMeNotInscribed(): ?bool
    {
        return $this->choiseMeNotInscribed;
    }

    public function setChoiseMeNotInscribed(?bool $choiseMeNotInscribed): static
    {
        $this->choiseMeNotInscribed = $choiseMeNotInscribed;

        return $this;
    }

    public function isFinishedEvents(): ?bool
    {
        return $this->finishedEvents;
    }

    public function setFinishedEvents(?bool $finishedEvents): static
    {
        $this->finishedEvents = $finishedEvents;

        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): static
    {
        $this->search = $search;

        return $this;
    }

//    /**
//     * @return Collection<int, Site>
//     */
    public function getSite(): ?Site
    {
        return $this->site;
    }
    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

}
