<?php

declare(strict_types=1);

namespace App\CourseAnswers\Entity\Product;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class ProductRepository
{
    private readonly EntityRepository $repo;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        $this->repo = $em->getRepository(Product::class);
    }

    public function add(Product $product): void
    {
        $this->em->persist($product);
    }

    public function findByCourseId(string $courseId): ?Product
    {
        return $this->repo->findOneBy(['courseId' => $courseId]);
    }

    public function get(ProductId $id): Product
    {
        $product = $this->repo->find($id);
        if (null === $product) {
            throw new DomainException('Product not found.');
        }
        return $product;
    }

    public function remove(Product $product): void
    {
        $this->em->remove($product);
    }
}
