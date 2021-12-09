<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Traversable;

class ProductRepository
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Gets all images with products.
     *
     * @param int $page
     * @param int $amount
     *
     * @return Traversable
     */
    public function findAllImagesWithProducts(int $page, int $amount): Traversable
    {
        $qb = $this->createQueryBuilder();
        $qb->leftJoin(Product::class, 'p');

        return (new Paginator($qb, $amount))
            ->paginate($page)
            ->getResults();
    }

    /**
     * Saves the product entity.
     *
     * @param Product|ProductImage $product
     */
    public function save(Product|ProductImage $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush($product);
    }

    /**
     * Creates QueryBuilder instance.
     *
     * @return QueryBuilder
     */
    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from(ProductImage::class, 'p');
    }
}