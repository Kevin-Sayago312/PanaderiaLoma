<?php

namespace App\Repository;

use App\Entity\Productos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Productos::class);
    }

    // Método personalizado para obtener la consulta de todos los productos
    public function findAllProductosQuery()
    {
        return $this->createQueryBuilder('p')
            ->getQuery();
    }
}

