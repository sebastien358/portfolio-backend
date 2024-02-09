<?php

namespace App\Entity;

use DateTime;
use App\Repository\CvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CvRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cv'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['cv'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['cv'])]
    private ?DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'cv', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['cv'])]
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
            $picture->setCv($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getCv() === $this) {
                $picture->setCv(null);
            }
        }

        return $this;
    }
}
