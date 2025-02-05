<?php

namespace App\Repository;

use App\Entity\TentativePinFailed;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TentativePinFailedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TentativePinFailed::class);
    }

    /**
     * Trouve toutes les tentatives de PIN échouées pour un utilisateur spécifique.
     * 
     * @param Utilisateur $utilisateur L'utilisateur pour lequel récupérer les tentatives échouées.
     * @return TentativePinFailed[] Retourne un tableau d'objets TentativePinFailed.
     */
    public function findByUtilisateur($utilisateur): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getResult();
    }
}
