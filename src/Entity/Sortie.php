<?php

namespace App\Entity;

use App\EventListener\SortieCancelListener;
use App\EventListener\SortieEditListener;
use App\EventListener\SortieListener;
use App\EventListener\SortiePublishListener;
use App\EventListener\SortieRegisterListener;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[ORM\Entity(repositoryClass: SortieRepository::class)]

/** @ORM\EntityListeners({
 *     SortieListener::class,
 *     SortieEditListener::class,
* })
 * @IgnoreAnnotation("ORM\EntityListeners")
*/
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHourStart = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateLimitInscription = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $maxInscriptions = null;

    #[ORM\Column(length: 400, nullable: true)]
    private ?string $info = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    #[ORM\ManyToOne(inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'organisedSorties')]
    private ?Participant $organizer = null;

    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'sorties')]
    private Collection $participants;

    #[ORM\ManyToOne(targetEntity: Sortie::class, inversedBy: 'sorties')]
    #[ORM\JoinColumn(name:"city_id", referencedColumnName:"id",nullable: true)]
    private City $city;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTime $updatedAt;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = new DateTime;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
    }



    private $canShowDetailClosure;

    /**
     * @return mixed
     */
    public function getCanShowDetailClosure()
    {
        return $this->canShowDetailClosure;
    }


    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->updatedAt = new DateTime();
    }

    public function __toString()
    {
        return sprintf(
            "%s, %s, %dh, %s, %d places, %s, %s, %s, %s, %s, %s",
            $this->getName(),
            $this->getDateHourStart()->format('Y-m-d H:i'),
            $this->getDuration(),
            $this->getDateLimitInscription()->format('Y-m-d H:i'),
            $this->getMaxInscriptions(),
            $this->getInfo(),
            $this->getPlace(),
            $this->getCity(),
            $this->getSite(),
            $this->getStatus(),
            $this->getOrganizer()
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

    public function getDateHourStart(): ?\DateTimeInterface
    {
        return $this->dateHourStart;
    }

    public function setDateHourStart(\DateTimeInterface $dateHourStart): static
    {
        $this->dateHourStart = $dateHourStart;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateLimitInscription(): ?\DateTimeInterface
    {
        return $this->dateLimitInscription;
    }

    public function setDateLimitInscription(\DateTimeInterface $dateLimitInscription): static
    {
        $this->dateLimitInscription = $dateLimitInscription;

        return $this;
    }

    public function getMaxInscriptions(): ?int
    {
        return $this->maxInscriptions;
    }

    public function setMaxInscriptions(int $maxInscriptions): static
    {
        $this->maxInscriptions = $maxInscriptions;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }


    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addSortie($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSortie($this);
        }

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): void
    {
        $this->motif = $motif;
    }

    public function setCanShowDetailClosure(\Closure $canShowDetailClosure): void
    {
        $this->canShowDetailClosure = $canShowDetailClosure;
    }

    public function isUserInscrit(UserInterface $user): bool
    {
        foreach ($this->participants as $participant) {
            if ($participant->getUser() === $user) {
                return true;
            }
        }
        return false;
    }




}
