<?php

namespace App\Entity;

use DateTime;
use App\Repository\ExperienceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['experiences', 'experience'])]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    #[Groups(['experiences', 'experience'])]
    private ?string $year = null;

    #[ORM\Column(length: 128)]
    #[Groups(['experiences', 'experience'])]
    private ?string $job = null;

    #[ORM\Column(length: 128)]
    #[Groups(['experiences', 'experience'])]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['experiences', 'experience'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['experiences', 'experience'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['experiences', 'experience'])]
    private ?DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'experience', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['experiences', 'experience'])]
    private Collection $pictures;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
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
            $picture->setExperience($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getExperience() === $this) {
                $picture->setExperience(null);
            }
        }

        return $this;
    }
}
