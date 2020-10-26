<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(options={"auto_increment": 100})
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"commandjs"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "Email invalide.")
     * @Groups({"commandjs"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^(?:\+689)?(87|89|92|40)(\d{6})$/")
     * @Groups({"commandjs"})
     */
    private $phone;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"commandjs"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"commandjs"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"commandjs"})
     */
    private $comment;

    /**
     * @ORM\Column(type="array")
     * @Groups({"commandjs"})
     */
    private $details = [];

    /**
     * @ORM\ManyToMany(targetEntity=Meal::class, inversedBy="commands")
     * @Groups({"commandjs"})
     */
    private $meals;

    /**
     * @ORM\ManyToMany(targetEntity=Provider::class, inversedBy="commands")
     * @Groups({"commandjs"})
     */
    private $providers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"commandjs"})
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"commandjs"})
     */
    private $address;

    /**
     * @ORM\Column(type="datetimetz")
     * @Groups({"commandjs"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetimetz")
     * @Groups({"commandjs"})
     */
    private $commandAt;

    /**
     * @ORM\ManyToOne(targetEntity=Lambda::class, inversedBy="commands")
     */
    private $lambda;

    /**
     * Code promo.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"commandjs"})
     */
    private $validate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="array")
     * @Groups({"commandjs"})
     */
    private $validation = [];

    public function __construct()
    {
        $this->meals = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->providers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Meal[]
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(Meal $meal): self
    {
        if (!$this->meals->contains($meal)) {
            $this->meals[] = $meal;
        }

        return $this;
    }

    public function removeMeal(Meal $meal): self
    {
        if ($this->meals->contains($meal)) {
            $this->meals->removeElement($meal);
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function findProvider(Provider $provider): bool
    {
        return $this->providers->contains($provider);
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->contains($provider)) {
            $this->providers->removeElement($provider);
        }

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCommandAt(): ?\DateTimeInterface
    {
        return $this->commandAt;
    }

    public function setCommandAt(\DateTimeInterface $commandAt): self
    {
        $this->commandAt = $commandAt;

        return $this;
    }

    public function getTimezone(): ?\DateTimeInterface
    {
        return $this->timezone;
    }

    public function setTimezone(\DateTimeInterface $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLambda(): ?Lambda
    {
        return $this->lambda;
    }

    public function setLambda(?Lambda $lambda): self
    {
        $this->lambda = $lambda;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(?bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getValidation(): ?array
    {
        return $this->validation;
    }

    public function setValidation(Provider $provider, bool $bool): self
    {
        $this->validation[$provider->getId()] = $bool;

        return $this;
    }
}
