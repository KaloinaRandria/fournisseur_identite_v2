<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Jeton;
use App\Entity\JetonAuthentification;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthMobileController extends AbstractController
{
    private $authService;
    private $entityManager;

    public function __construct(AuthService $authService, EntityManagerInterface $entityManager)
    {
        $this->authService = $authService;
        $this->entityManager = $entityManager;
    }

    #[Route('/authMobile', name: 'authMobile', methods: ['POST'])]
    public function authMobile(Request $request): JsonResponse
    {
        try {
            // Récupérer les données envoyées dans la requête
            $data = json_decode($request->getContent(), true);
    
            // Validation des données
            if (!isset($data['mail'], $data['mdp'])) {
                return $this->createErrorResponse(400, 'Données manquantes');
            }
    
            // Vérification si l'utilisateur existe dans la base de données
            $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['mail' => $data['mail']]);
            if (!$utilisateur) {
                return $this->createErrorResponse(400, 'Aucun utilisateur associé à ce mail');
            }
    
            // Vérification du mot de passe
            if (!$this->authService->checkLogin($utilisateur, $data['mdp'])) {
                return $this->createErrorResponse(400, 'Mot de passe incorrect');
            }
    
            $duree_jeton = -1;
            if (isset($data['duree_jeton'])) {
                $duree_jeton = $data['duree_jeton'];
            }
    
            $jeton = new Jeton($duree_jeton);
            $this->entityManager->persist($jeton);
            $this->entityManager->flush();
    
            $jeton_authentification = new JetonAuthentification($utilisateur, $jeton);
            $this->entityManager->persist($jeton_authentification);
            $this->entityManager->flush();
    
            // Si l'authentification est réussie, retourner le jeton et l'utilisateur
            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'message' => 'Veuillez vérifier votre e-mail pour voir votre pin.',
                    'jeton' => $jeton->getJeton(),  // Assurez-vous que la méthode getToken() retourne le jeton que vous souhaitez
                    'utilisateur' => [
                        'id' => $utilisateur->getId(),
                        'mail' => $utilisateur->getMail(),
                        'nom' => $utilisateur->getNom(), // Ajouter d'autres attributs de l'utilisateur si nécessaire
                        'mdp' => $utilisateur->getMdp(),
                        'dtn'=> $utilisateur->getDateNaissance()
                    ]
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Gestion des erreurs internes
            return $this->createErrorResponse(500, $e->getMessage());
        }
    }
    

    /**
     * Méthode utilitaire pour créer des réponses d'erreur uniformes
     */
    private function createErrorResponse(int $code, string $message): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'data' => null,
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }
}
