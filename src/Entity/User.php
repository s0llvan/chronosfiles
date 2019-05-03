<?php
// /src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email address already used !")
 * @UniqueEntity(fields="username", message="Username already used !")
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\NotBlank(message="Username is not required")
	 * @Assert\Length(
	 *       min = 3,
	 *       minMessage = "Username is too short. It should have {{ limit }} characters or more.",
	 *       max = 12,
	 *       maxMessage = "Username is too long. It should have {{ limit }} characters or less."
	 * )
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\NotBlank(message="Email is required.")
	 * @Assert\Email(message="Email is not valid.")
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=64)
	 * @Assert\NotBlank(message="Password is required.")
	 * @Assert\Length(
	 *       min = 8,
	 *       minMessage = "Password is too short. It should have {{ limit }} characters or more.",
	 *       max = 64,
	 *       maxMessage = "Password is too long. It should have {{ limit }} characters or less."
	 * )
	 */
	private $password;

	/**
	 * @ORM\Column(type="text")
	 */
	private $encryptionKey;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="user", orphanRemoval=true)
	 */
	private $files;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="user", orphanRemoval=true)
	 */
	private $categories;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $emailConfirmed = false;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $emailConfirmationToken;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $passwordResetToken;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $passwordResetTokenLast;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $emailConfirmation;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $emailConfirmationLast;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastLogin;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $role;

	public function __construct()
	{
		$this->files = new ArrayCollection();
		$this->categories = new ArrayCollection();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * Retour le salt qui a servi à coder le mot de passe
	 *
	 * {@inheritdoc}
	 */
	public function getSalt(): ?string
	{
		// See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
		// we're using bcrypt in security.yml to encode the password, so
		// the salt value is built-in and you don't have to generate one

		return null;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * {@inheritdoc}
	 */
	public function eraseCredentials(): void
	{
		// Nous n'avons pas besoin de cette methode car nous n'utilions pas de plainPassword
		// Mais elle est obligatoire car comprise dans l'interface UserInterface
		// $this->plainPassword = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize(): string
	{
		return serialize([$this->id, $this->username, $this->password]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized): void
	{
		[$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
	}

	public function getEncryptionKey(): ?string
	{
		return $this->encryptionKey;
	}

	public function setEncryptionKey(string $encryptionKey): self
	{
		$this->encryptionKey = $encryptionKey;

		return $this;
	}

	/**
	 * @return Collection|File[]
	 */
	public function getFiles(): Collection
	{
		return $this->files;
	}

	public function addFile(File $file): self
	{
		if (!$this->files->contains($file)) {
			$this->files[] = $file;
			$file->setUser($this);
		}

		return $this;
	}

	public function removeFile(File $file): self
	{
		if ($this->files->contains($file)) {
			$this->files->removeElement($file);
			// set the owning side to null (unless already changed)
			if ($file->getUser() === $this) {
				$file->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Category[]
	 */
	public function getCategories(): Collection
	{
		return $this->categories;
	}

	public function addCategory(Category $category): self
	{
		if (!$this->categories->contains($category)) {
			$this->categories[] = $category;
			$category->setUser($this);
		}

		return $this;
	}

	public function removeCategory(Category $category): self
	{
		if ($this->categories->contains($category)) {
			$this->categories->removeElement($category);
			// set the owning side to null (unless already changed)
			if ($category->getUser() === $this) {
				$category->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|File[]
	 */
	public function getUncategorizedFiles(): Collection
	{
		return $this->files->filter(function ($file) {
			return !$file->getCategory();
		});
	}

	public function getEmailConfirmed(): ?bool
	{
		return $this->emailConfirmed;
	}

	public function setEmailConfirmed(bool $emailConfirmed): self
	{
		$this->emailConfirmed = $emailConfirmed;

		return $this;
	}

	public function getEmailConfirmationToken(): ?string
	{
		return $this->emailConfirmationToken;
	}

	public function setEmailConfirmationToken(?string $emailConfirmationToken): self
	{
		$this->emailConfirmationToken = $emailConfirmationToken;

		return $this;
	}

	public function getPasswordResetToken(): ?string
	{
		return $this->passwordResetToken;
	}

	public function setPasswordResetToken(?string $passwordResetToken): self
	{
		$this->passwordResetToken = $passwordResetToken;

		return $this;
	}

	public function getPasswordResetTokenLast(): ?\DateTimeInterface
	{
		return $this->passwordResetTokenLast;
	}

	public function setPasswordResetTokenLast(?\DateTimeInterface $passwordResetTokenLast): self
	{
		$this->passwordResetTokenLast = $passwordResetTokenLast;

		return $this;
	}

	public function getEmailConfirmation(): ?string
	{
		return $this->emailConfirmation;
	}

	public function setEmailConfirmation(?string $emailConfirmation): self
	{
		$this->emailConfirmation = $emailConfirmation;

		return $this;
	}

	public function getEmailConfirmationLast(): ?\DateTimeInterface
	{
		return $this->emailConfirmationLast;
	}

	public function setEmailConfirmationLast(?\DateTimeInterface $emailConfirmationLast): self
	{
		$this->emailConfirmationLast = $emailConfirmationLast;

		return $this;
	}

	public function getLastLogin(): ?\DateTimeInterface
	{
		return $this->lastLogin;
	}

	public function setLastLogin(?\DateTimeInterface $lastLogin): self
	{
		$this->lastLogin = $lastLogin;

		return $this;
	}

	public function getRoles(): array
	{
		$slug = 'ROLE_USER';

		if ($this->getRole()) {
			$slug = $this->getRole()->getSlug();
		}

		return [$slug];
	}

	public function getRole(): ?Role
	{
		return $this->role;
	}

	public function setRole(?Role $role): self
	{
		$this->role = $role;

		return $this;
	}
}
