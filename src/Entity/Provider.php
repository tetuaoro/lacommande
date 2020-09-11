<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProviderRepository::class)
 */
class Provider
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\SequenceGenerator(sequenceName="id", initialValue=100)
     * @ORM\Column(type="integer")
     * @Groups({"commandjs"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"commandjs"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $opentime;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $closetime;

    /**
     * @ORM\OneToMany(targetEntity=Meal::class, mappedBy="provider")
     */
    private $meals;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     * @Groups({"commandjs"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="provider")
     */
    private $menus;

    /**
     * @ORM\OneToMany(targetEntity=Subuser::class, mappedBy="provider", orphanRemoval=true)
     */
    private $subusers;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="providers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bgImg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkfb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkinsta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linktwitter;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $imgInfo = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Command::class, mappedBy="providers")
     */
    private $commands;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minPriceDelivery;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->meals = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->subusers = new ArrayCollection();
        $this->commands = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getOpentime(): ?\DateTimeInterface
    {
        return $this->opentime;
    }

    public function setOpentime(\DateTimeInterface $opentime): self
    {
        $this->opentime = $opentime;

        return $this;
    }

    public function getClosetime(): ?\DateTimeInterface
    {
        return $this->closetime;
    }

    public function setClosetime(\DateTimeInterface $closetime): self
    {
        $this->closetime = $closetime;

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
            $meal->setProvider($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): self
    {
        if ($this->meals->contains($meal)) {
            $this->meals->removeElement($meal);
            // set the owning side to null (unless already changed)
            if ($meal->getProvider() === $this) {
                $meal->setProvider(null);
            }
        }

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
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->setProvider($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->contains($menu)) {
            $this->menus->removeElement($menu);
            // set the owning side to null (unless already changed)
            if ($menu->getProvider() === $this) {
                $menu->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subuser[]
     */
    public function getSubusers(): Collection
    {
        return $this->subusers;
    }

    public function addSubuser(Subuser $subuser): self
    {
        if (!$this->subusers->contains($subuser)) {
            $this->subusers[] = $subuser;
            $subuser->setProvider($this);
        }

        return $this;
    }

    public function removeSubuser(Subuser $subuser): self
    {
        if ($this->subusers->contains($subuser)) {
            $this->subusers->removeElement($subuser);
            // set the owning side to null (unless already changed)
            if ($subuser->getProvider() === $this) {
                $subuser->setProvider(null);
            }
        }

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
            $command->addProvider($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            // set the owning side to null (unless already changed)
            if ($command->getProviders() === $this) {
                $command->removeProvider($this);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBgImg(): ?string
    {
        return $this->bgImg;
    }

    public function setBgImg(?string $bgImg): self
    {
        $this->bgImg = $bgImg;

        return $this;
    }

    public function getLinkfb(): ?string
    {
        return $this->linkfb;
    }

    public function setLinkfb(?string $linkfb): self
    {
        $this->linkfb = $linkfb;

        return $this;
    }

    public function getLinkinsta(): ?string
    {
        return $this->linkinsta;
    }

    public function setLinkinsta(?string $linkinsta): self
    {
        $this->linkinsta = $linkinsta;

        return $this;
    }

    public function getLinktwitter(): ?string
    {
        return $this->linktwitter;
    }

    public function setLinktwitter(?string $linktwitter): self
    {
        $this->linktwitter = $linktwitter;

        return $this;
    }

    public function getImgInfo(): ?array
    {
        return $this->imgInfo;
    }

    public function setImgInfo(?array $imgInfo): self
    {
        $this->imgInfo = $imgInfo;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

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

    public function getMinPriceDelivery(): ?int
    {
        return $this->minPriceDelivery;
    }

    public function setMinPriceDelivery(?int $minPriceDelivery): self
    {
        $this->minPriceDelivery = $minPriceDelivery;

        return $this;
    }
}
