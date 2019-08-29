<?php

namespace App\Repository;

use App\Entity\Product;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findLatest(int $page): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('c')
            ->innerJoin('p.category', 'c')
            ->where('p.addedOn <= :now')
            ->orderBy('p.addedOn', 'DESC')
            ->setParameter('now', new \DateTime());

        return (new Paginator($qb))->paginate($page);
    }
}
