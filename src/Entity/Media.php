<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trick_id = null;

    #[ORM\Column(length: 10, type: 'string')]
    private ?string $type = null;

    #[ORM\Column(length: 500)]
    private ?string $media = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickId(): ?Trick
    {
        return $this->trick_id;
    }

    public function setTrickId(?Trick $trick_id): static
    {
        $this->trick_id = $trick_id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(string $media): static
    {
        $this->media = $media;

        return $this;
    }
}
