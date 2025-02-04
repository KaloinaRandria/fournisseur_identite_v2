<?php

namespace App\Service;

use App\Entity\JetonAuthentification;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JetonAuthentificationRepository;

class DeconnexionService 
{
    private JetonAuthentificationRepository $jetonAuthentificationRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JetonAuthentificationRepository $jetonAuthentificationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->jetonAuthentificationRepository = $jetonAuthentificationRepository;
        $this->entityManager = $entityManager;
    }

    public function deconnexion(string $jeton): void
    {
        $jetonAuth = $this->jetonAuthentificationRepository->findOneBy(['jeton' => $jeton]);

        if (!$jetonAuth) {
            throw new \Exception("Jeton non trouvé.");
        }

        $jetonEntity = $jetonAuth->getJeton();
        $jetonEntity->getExpirationUtil()->setDateExpiration(new \DateTime());

        $this->entityManager->flush();
    }
}
