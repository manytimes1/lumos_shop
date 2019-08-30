<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllCustomersByRole(string $name)
    {
        return $this->createQueryBuilder('u')
            ->join('u.roles', 'r')
            ->where('r.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }
}
