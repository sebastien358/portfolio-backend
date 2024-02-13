<?php

namespace App\Entity;

use DateTime;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project', 'projects'])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['project', 'projects'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['project', 'projects'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project', 'projects'])]
    private ?string $objectif = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project', 'projects'])]
    private ?string $fonctionnality = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project', 'projects'])]
    private ?string $competence = null;

    #[ORM\Column]
    #[Groups(['project', 'projects'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['project', 'projects'])]
    private ?DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'project', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['project', 'projects'])]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getFonctionnality(): ?string
    {
        return $this->fonctionnality;
    }

    public function setFonctionnality(string $fonctionnality): static
    {
        $this->fonctionnality = $fonctionnality;

        return $this;
    }

    public function getCompetence(): ?string
    {
        return $this->competence;
    }

    public function setCompetence(string $competence): static
    {
        $this->competence = $competence;

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
            $picture->setProject($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getProject() === $this) {
                $picture->setProject(null);
            }
        }

        return $this;
    }
}
