<?php

namespace App\Repository;

use App\Entity\JetonAuthentification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JetonAuthentificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JetonAuthentification::class);
    }

    /**
     * Insère un nouvel enregistrement dans la table jeton_inscription.
     */
    public function insertJetonAuthentification(JetonAuthentification $JetonAuthentification): void
    {
        $this->_em->persist($JetonAuthentification);
        $this->_em->flush();
    }

    /**
     * Recherche un jeton d'inscription par son token.
     */
    public function findByToken(string $token): ?JetonAuthentification
    {
        return $this->createQueryBuilder('ja')
            ->join('ja.jeton', 'j')
            ->where('j.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime un jeton après validation.
     */
    public function remove(int $id): void
    {
        $JetonAuthentification = $this->find($id);

        if ($JetonAuthentification) {
            $this->_em->remove($JetonAuthentification);
            $this->_em->flush();
        }
    }


}
