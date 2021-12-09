<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'product')]
class Product
{
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO'), ORM\Column]
    private int $id;

    /**
     * Product constructor.
     *
     * @param string $title
     * @param DateTimeInterface $createdAt
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: ProductImage::class)]
        private string $title,

        #[ORM\Column]
        private DateTimeInterface $createdAt = new DateTimeImmutable('now'),
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