<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
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
    private $model;

    /**
     * @ORM\Column(type="string", name="main_board", length=255)
     */
    private $mainBoard;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cpu;

    /**
     * @ORM\Column(type="integer")
     */
    private $ram;

    /**
     * @ORM\Column(type="string", name="ram_type", length=255)
     */
    private $ramType;

    /**
     * @ORM\Column(type="integer")
     */
    private $hdd;

    /**
     * @ORM\Column(type="string", name="video_card")
     */
    private $videoCard;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $odd;

    /**
     * @ORM\Column(type="integer")
     */
    private $case;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $image;

    /**
     * Many products have one category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Cart", mappedBy="product", cascade={"persist"})
     */
    private $carts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAvailable;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedOn;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="product")
     */
    private $orders;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->addedOn = new \DateTime('now');
        $this->orders = new ArrayCollection();
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

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainBoard()
    {
        return $this->mainBoard;
    }

    /**
     * @param mixed $mainBoard
     */
    public function setMainBoard($mainBoard): void
    {
        $this->mainBoard = $mainBoard;
    }

    /**
     * @return mixed
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * @param mixed $cpu
     */
    public function setCpu($cpu): void
    {
        $this->cpu = $cpu;
    }

    /**
     * @return mixed
     */
    public function getRam()
    {
        return $this->ram;
    }

    /**
     * @param mixed $ram
     */
    public function setRam($ram): void
    {
        $this->ram = $ram;
    }

    /**
     * @return mixed
     */
    public function getRamType()
    {
        return $this->ramType;
    }

    /**
     * @param mixed $ramType
     */
    public function setRamType($ramType): void
    {
        $this->ramType = $ramType;
    }

    /**
     * @return mixed
     */
    public function getHdd()
    {
        return $this->hdd;
    }

    /**
     * @param mixed $hdd
     */
    public function setHdd($hdd): void
    {
        $this->hdd = $hdd;
    }

    /**
     * @return mixed
     */
    public function getVideoCard()
    {
        return $this->videoCard;
    }

    /**
     * @param mixed $videoCard
     */
    public function setVideoCard($videoCard): void
    {
        $this->videoCard = $videoCard;
    }

    /**
     * @return mixed
     */
    public function getOdd()
    {
        return $this->odd;
    }

    /**
     * @param mixed $odd
     */
    public function setOdd($odd): void
    {
        $this->odd = $odd;
    }

    /**
     * @return mixed
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * @param mixed $case
     */
    public function setCase($case): void
    {
        $this->case = $case;
    }

    /**
     * @return mixed
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * @param mixed $carts
     */
    public function setCarts($carts): void
    {
        $this->carts = $carts;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getAddedOn(): ?\DateTimeInterface
    {
        return $this->addedOn;
    }

    public function setAddedOn(\DateTimeInterface $addedOn): self
    {
        $this->addedOn = $addedOn;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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
            $order->setProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getProduct() === $this) {
                $order->setProduct(null);
            }
        }

        return $this;
    }
}
