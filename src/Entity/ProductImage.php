<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping AS ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'product_image')]
class ProductImage
{
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO'), ORM\Column]
    private int $id;

    /**
     * ProductImage constructor.
     *
     * @param string $title
     * @param Product $product
     * @param DateTimeInterface $createdAt
     */
    public function __construct(
        #[ORM\Column]
        private string $title,

        #[ORM\OneToMany(mappedBy: Product::class)]
        private Product $product,

        #[ORM\Column]
        private DateTimeInterface $createdAt = new \DateTimeImmutable('now'),
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}