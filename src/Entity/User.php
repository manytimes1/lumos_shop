<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\Length(
     *      min = 4,
     *      max = 255,
     *      minMessage = "Username must be at least {{ limit }} characters",
     *      maxMessage = "Username cannot be longer than {{ limit }} characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\Length(
     *      min = 4,
     *      max = 255,
     *      minMessage = "Email must be at least {{ limit }} characters",
     *      maxMessage = "Email cannot be longer than {{ limit }} characters"
     * )
     */
    private $email;

    /**
     * @Assert\Length(
     *      min = 6,
     *      max = 255,
     *      minMessage = "Password must be at least {{ limit }} characters",
     *      maxMessage = "Password cannot be longer than {{ limit }} characters"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     */
    private $roles;

    /**
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user", cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity="Cart", mappedBy="user")
     */
    private $carts;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="user")
     */
    private $createdProducts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    private $orders;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function getCreatedProducts()
    {
        return $this->createdProducts;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $roles = [];

        foreach ($this->roles as $role) {
            /** @var Role $role */
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    public function addRole(Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isEditor()
    {
        return in_array('ROLE_EDITOR', $this->getRoles());
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
