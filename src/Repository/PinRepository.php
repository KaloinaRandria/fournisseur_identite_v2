<?php

namespace App\Repository;

use App\Entity\Pin;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pin>
 *
 * @method Pin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pin[]    findAll()
 * @method Pin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pin::class);
    }

    /**
     * Cette méthode permet de récupérer un PIN par son utilisateur.
     * @param Utilisateur $utilisateur
     * @return Pin|null Retourne un PIN pour un utilisateur donné ou null.
     */
    public function findByUtilisateur($utilisateur): ?Pin
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
