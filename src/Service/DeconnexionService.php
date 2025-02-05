<?php

namespace App\Service;

use App\Repository\TentativePinFailedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JetonRepository;
use App\Repository\PinRepository;
use App\Entity\Utilisateur;
use App\Entity\Pin;
use App\Entity\TentativePinFailed;

class DeconnexionService 
{
    private JetonRepository $jetonRepository;
    private TentativePinFailedRepository $tentativePinFailedRepository;
    private PinRepository $pinRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JetonRepository $jetonRepository,
        TentativePinFailedRepository $tentativePinFailedRepository,
        PinRepository $pinRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->jetonRepository = $jetonRepository;
        $this->entityManager = $entityManager;
        $this->tentativePinFailedRepository = $tentativePinFailedRepository;
        $this->pinRepository = $pinRepository;

    }

    public function deconnexion(string $jeton, int $idUtilisateur): void
    {
        // Trouver le jeton dans la table 'jeton' directement
        $jetonEntity = $this->jetonRepository->findOneBy(['jeton' => $jeton]);

        // Si le jeton n'est pas trouvé, lever une exception
        if (!$jetonEntity) {
            throw new \Exception("Jeton non trouvé.");
        }

        // Trouver l'utilisateur par son ID
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find($idUtilisateur);

        if (!$utilisateur) {
            throw new \Exception("Utilisateur non trouvé.");
        }

        // Trouver la tentative de PIN échouée associée à cet utilisateur
        $tentativePin = $this->tentativePinFailedRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Si une tentative de PIN échouée est trouvée, la supprimer
        if ($tentativePin) {
            $this->entityManager->remove($tentativePin);
        }

        // Trouver le PIN associé à l'utilisateur
        $pin = $this->entityManager->getRepository(Pin::class)->findOneBy(['utilisateur' => $utilisateur]);

        // Si un PIN est trouvé, le supprimer
        if ($pin) {
            $this->entityManager->remove($pin);
        }

        // Modifier la date d'expiration du jeton
        $jetonEntity->getExpirationUtil()->setDateExpirationFromNow();

        // Sauvegarder les changements dans la base de données
        $this->entityManager->flush();
    }


}
