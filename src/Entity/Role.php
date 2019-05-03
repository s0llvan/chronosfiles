<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $uploadFileSizeLimit;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $storageSizeLimit;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="role", orphanRemoval=true)
	 */
	private $users;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $slug;

	public function __construct()
	{
		$this->users = new ArrayCollection();
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

	public function getUploadFileSizeLimit(): ?int
	{
		return $this->uploadFileSizeLimit;
	}

	public function setUploadFileSizeLimit(?int $uploadFileSizeLimit): self
	{
		$this->uploadFileSizeLimit = $uploadFileSizeLimit;

		return $this;
	}

	public function getStorageSizeLimit(): ?int
	{
		return $this->storageSizeLimit;
	}

	public function setStorageSizeLimit(?int $storageSizeLimit): self
	{
		$this->storageSizeLimit = $storageSizeLimit;

		return $this;
	}

	/**
	 * @return Collection|User[]
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(User $user): self
	{
		if (!$this->users->contains($user)) {
			$this->users[] = $user;
			$user->setRole($this);
		}

		return $this;
	}

	public function removeUser(User $user): self
	{
		if ($this->users->contains($user)) {
			$this->users->removeElement($user);
			// set the owning side to null (unless already changed)
			if ($user->getRole() === $this) {
				$user->setRole(null);
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
}
