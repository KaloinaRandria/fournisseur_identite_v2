<?php

namespace App\Controller;

use App\Service\DeconnexionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DeconnexionController extends AbstractController
{
    private $deconnexionService;
    private $entityManager;

    // Correction de l'injection du service avec la bonne casse
    public function __construct(
        DeconnexionService $deconnexionService,
        EntityManagerInterface $entityManager
    ) {
        // Utilisation de la bonne casse pour la variable
        $this->deconnexionService = $deconnexionService;
        $this->entityManager = $entityManager;
    }

    #[Route('/deconnexion', name: 'deconnexion', methods: ['POST'])]
    public function deconnexion(Request $request): JsonResponse
    {
        try {
            // Décodage du contenu de la requête
            $data = json_decode($request->getContent(), true);
    
            $jetonValeur = $data['jeton'] ?? null;
            $idUtilisateur = $data['id_utilisateur'] ?? null; // Récupère l'ID de l'utilisateur depuis la requête
    
            if (!$jetonValeur || !$idUtilisateur) {
                // Log si le jeton ou l'ID de l'utilisateur sont manquants
                error_log("Jeton ou ID de l'utilisateur manquants dans la requête.");
    
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Jeton ou ID de l\'utilisateur manquants dans la requête.'
                ], 400);
            }
    
            // Log des valeurs reçues
            error_log("Jeton reçu: " . $jetonValeur);
            error_log("ID Utilisateur reçu: " . $idUtilisateur);
    
            // Vérifie si le service est bien injecté
            if (!$this->deconnexionService) {
                error_log("Le service de déconnexion est null.");
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Service de déconnexion non disponible.'
                ], 500);
            }
    
            // Appel du service pour gérer la déconnexion avec l'ID de l'utilisateur
            $this->deconnexionService->deconnexion($jetonValeur, $idUtilisateur);
    
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Déconnexion réussie.'
            ], 200);
        } catch (\Exception $e) {
            // Log de l'exception
            error_log("Erreur dans deconnexion: " . $e->getMessage());
    
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }
    
}
