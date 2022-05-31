<?php

namespace App\Entity;

use App\Repository\VMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VMRepository::class)
 */
class VM
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sshPort;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=VMUser::class, mappedBy="vm", orphanRemoval=true)
     */
    private $vmUsers;

    public function __construct()
    {
        $this->vmUsers = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getsshPort(): ?int
    {
        return $this->sshPort;
    }

    public function setsshPort(int $sshPort): self
    {
        $this->sshPort = $sshPort;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|VMUser[]
     */
    public function getVmUsers(): Collection
    {
        return $this->vmUsers;
    }

    public function addVmUser(VMUser $vmUser): self
    {
        if (!$this->vmUsers->contains($vmUser)) {
            $this->vmUsers[] = $vmUser;
            $vmUser->setVm($this);
        }

        return $this;
    }

    public function removeVmUser(VMUser $vmUser): self
    {
        if ($this->vmUsers->removeElement($vmUser)) {
            // set the owning side to null (unless already changed)
            if ($vmUser->getVm() === $this) {
                $vmUser->setVm(null);
            }
        }

        return $this;
    }
}
