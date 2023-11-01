<?php

namespace App\Entity;

use App\Repository\PasswordTokenRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordTokenRepository::class)]
class PasswordToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'passwordToken')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expiry = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiry(): ?\DateTimeInterface
    {
        return $this->expiry;
    }

    public function setExpiry(\DateTimeInterface $expiry): static
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function isValid(\DateTimeInterface $expiry): bool
    {
        $currentDateTime = new DateTime('now', new \DateTimeZone('Europe/Paris'));
        
        if ($expiry > $currentDateTime)
        {
            // Token expired
            return true;
        }else{
            // Token valid
            return false;
        }
    }
}
