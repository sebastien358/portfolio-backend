<?php

namespace App\Entity;

use DateTime;
use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['formations', 'formation'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['formations', 'formation'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['formations', 'formation'])]
    private ?DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'formation', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['formations', 'formation'])]
    private Collection $pictures;

    #[ORM\Column(length: 64)]
    #[Groups(['formations', 'formation'])]
    private ?string $year = null;

    #[ORM\Column(length: 64)]
    #[Groups(['formations', 'formation'])]
    private ?string $diploma = null;

    #[ORM\Column(length: 64)]
    #[Groups(['formations', 'formation'])]
    private ?string $job = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['formations', 'formation'])]
    private ?string $techno = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    #[PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setFormation($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getFormation() === $this) {
                $picture->setFormation(null);
            }
        }

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    public function setDiploma(string $diploma): static
    {
        $this->diploma = $diploma;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getTechno(): ?string
    {
        return $this->techno;
    }

    public function setTechno(string $techno): static
    {
        $this->techno = $techno;

        return $this;
    }
}
