<?php

namespace App\Controller;

use App\Entity\JetonAuthentification;
use App\Entity\Pin;
use App\Entity\Jeton;
use App\Entity\TentativeMdpFailed;
use App\Entity\TentativePinFailed;
use App\Entity\Utilisateur;
use App\Service\DeconnexionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailService;
use OpenApi\Annotations as OA;


class DeconnexionController extends AbstractController
{
    private $deconnexionService;
    private $entityManager; // Ajouter EntityManagerInterface

    public function __construct(
        DeconnexionService $deconnexionService,
        EntityManagerInterface $entityManager // Injecter EntityManagerInterface
    ) {
        $this->DeconnexionService = $deconnexionService;
        $this->entityManager = $entityManager; // Initialiser EntityManagerInterface
    }

    #[Route('/deconnexion', name: 'deconnexion', methods: ['POST'])]
    public function deconnexion(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $jetonValeur = $data['jeton'] ?? null;

            if (!$jetonValeur) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Jeton manquant dans la requête.'
                ], 400);
            }

            // Appel du service pour gérer la déconnexion
            $this->deconnexionService->deconnexion($jetonValeur);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Déconnexion réussie.'
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }



}