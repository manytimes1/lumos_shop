<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("profiles")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", nullable=true, type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(name="phone", type="string", length=255, unique=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="location", nullable=true, type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(name="city", nullable=true, type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(name="state", nullable=true, type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(name="zip_code", nullable=true, type="integer")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $cash;

    /**
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName($lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location): void
    {
        $this->location = $location;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state): void
    {
        $this->state = $state;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function setZipCode($zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCash()
    {
        return $this->cash;
    }

    public function setCash($cash): self
    {
        $this->cash = $cash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }
}
