<?php

namespace App\Entity;

use App\Repository\SubuserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(options={"auto_increment": 100})
 * @ORM\Entity(repositoryClass=SubuserRepository::class)
 */
class Subuser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("subjs")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("subjs")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="subusers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="array")
     * @Groups("subjs")
     */
    private $roles = [];

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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
