<?php

namespace App\Entity;

use App\Annotation\Link;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Link("self", route="user_index", params={"id": "object.getId()"})
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user:read")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(message="First name is mandatory")
     * @Assert\Type(type={"alpha"})
     */
    private ?string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(message="Last name is mandatory")
     * @Assert\Type(type={"alpha"})
     */
    private ?string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(message="Phone number is mandatory")
     * @Assert\Type(type={"digit"})
     */
    private ?string $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank(message="Email address is mandatory")
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private ?string $emailAddress;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("user:read")
     */
    private DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Client $client;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("user:read")
     */
    private DateTime $updatedAt;


    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
