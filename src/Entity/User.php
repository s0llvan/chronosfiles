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
    * @Assert\NotBlank()
    * @Assert\Length(
    *       min = 3,
    *       max = 12
    * )
    */
    private $username;

    /**
    * @var string
    *
    * @ORM\Column(type="string", unique=true)
    * @Assert\NotBlank()
    * @Assert\Email()
    */
    private $email;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=64)
    * @Assert\NotBlank()
    * @Assert\Length(
    *       min = 8,
    *       max = 64
    * )
    */
    private $password;

    /**
    * @var array
    *
    * @ORM\Column(type="json")
    */
    private $roles = [];

    /**
    * @ORM\Column(type="string", length=255)
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
    * Retourne les rôles de l'user
    */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Afin d'être sûr qu'un user a toujours au moins 1 rôle
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
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
}
