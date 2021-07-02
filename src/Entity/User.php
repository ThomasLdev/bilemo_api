<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
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
     * @Groups("user:read")
     */
    private ?string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     */
    private ?string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
     */
    private ?string $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:read")
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
     * @Groups("user:read")
     */
    private ?Client $client;

    /*
    /**
     * @ORM\ManyToMany(targetEntity=Phone::class, mappedBy="user")
     */
    //private ArrayCollection $phones;

    public function __construct()
    {
        //$this->phones = new ArrayCollection();
        $this->createdAt = new DateTime();
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

    /*public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->addUser($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            $phone->removeUser($this);
        }

        return $this;
    }*/
}