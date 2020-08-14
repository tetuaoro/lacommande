<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MealRepository::class)
 */
class Meal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(min=8, minMessage="8 caractères minimum")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Assert\Length(min=20, minMessage="20 caractères minimum")
     * @ORM\Column(type="text", nullable=true)
     */
    private $recipe;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $picture = [];

    /**
     * @Assert\Length(min=20, minMessage="20 caractères minimum")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(500)
     * @Assert\LessThanOrEqual(7000)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="meals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Command::class, mappedBy="meals")
     */
    private $commands;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;

    /**
     * @ORM\Column(type="array")
     */
    private $imgInfo = [];

    /**
     * @ORM\OneToOne(targetEntity=Gallery::class, mappedBy="meal", cascade={"persist", "remove"})
     */
    private $gallery;

    /**
     * @ORM\ManyToMany(targetEntity=Tags::class, inversedBy="meals", cascade={"persist"})
     */
    private $tags;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalcommand;

    /**
     * @ORM\OneToOne(targetEntity=Menu::class, mappedBy="meal", cascade={"persist", "remove"})
     */
    private $menu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $delivery;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->commands = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function getRecipe(): ?string
    {
        return $this->recipe;
    }

    public function setRecipe(?string $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getPicture(): ?array
    {
        return $this->picture;
    }

    public function setPicture(?array $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->addMeals($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            $command->removeMeals($this);
        }

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getImgInfo(): ?array
    {
        return $this->imgInfo;
    }

    public function setImgInfo(array $imgInfo): self
    {
        $this->imgInfo = $imgInfo;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;

        // set (or unset) the owning side of the relation if necessary
        $newMeal = null === $gallery ? null : $this;
        if ($gallery->getMeal() !== $newMeal) {
            $gallery->setMeal($newMeal);
        }

        return $this;
    }

    /**
     * @return Collection|Tags[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getTotalcommand(): ?int
    {
        return $this->totalcommand;
    }

    public function setTotalcommand(?int $totalcommand): self
    {
        $this->totalcommand = $totalcommand;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(Menu $menu): self
    {
        $this->menu = $menu;

        // set the owning side of the relation if necessary
        if ($menu->getMeal() !== $this) {
            $menu->setMeal($this);
        }

        return $this;
    }

    public function commandPlus(): self
    {
        ++$this->totalcommand;

        return $this;
    }

    public function getDelivery(): ?bool
    {
        return $this->delivery;
    }

    public function setDelivery(?bool $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }
}
