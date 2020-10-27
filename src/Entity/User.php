<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`", options={"auto_increment": 100})
 * @UniqueEntity("email", message ="Vous êtes déjà enregistré(e) ?")
 * @UniqueEntity("username", message ="Ce nom d'utilisateur est déjà utilisé.")
 * @UniqueEntity("name", message ="Ce nom est déjà utilisé.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min=4, minMessage="le nom doit faire 4 caractères minimum")
     */
    private $name;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $createdAt;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @Assert\Length(min=8, minMessage="l'identifiant doit faire 8 caractères minimum")
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"settingjs"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="le mot de passe doit avoir 8 caractères minimum")
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\Email(message = "Une adresse mail.")
     * @Groups({"settingjs"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=6,max=9)
     * @Groups({"settingjs"})
     */
    private $ntahiti;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationEmail;

    /**
     * @ORM\OneToOne(targetEntity=Lambda::class, cascade={"persist", "remove"})
     */
    private $lambda;

    /**
     * @ORM\OneToOne(targetEntity=Delivery::class, cascade={"persist", "remove"})
     */
    private $delivery;

    /**
     * @ORM\OneToOne(targetEntity=Provider::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $provider;

    /**
     * @ORM\OneToOne(targetEntity=Subuser::class, cascade={"persist", "remove"})
     */
    private $subuser;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validate;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->roles = ['ROLE_USER'];
        $this->salt = md5(uniqid());
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNtahiti(): ?string
    {
        return $this->ntahiti;
    }

    public function setNtahiti(string $ntahiti): self
    {
        $this->ntahiti = $ntahiti;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getConfirmationEmail(): ?string
    {
        return $this->confirmationEmail;
    }

    public function setConfirmationEmail(?string $confirmationEmail): self
    {
        $this->confirmationEmail = $confirmationEmail;

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

    public function getSubuser(): ?Subuser
    {
        return $this->subuser;
    }

    public function setSubuser(?Subuser $subuser): self
    {
        $this->subuser = $subuser;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}
