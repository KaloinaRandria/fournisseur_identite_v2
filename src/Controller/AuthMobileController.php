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

    #[Route('/authMobile2', name: 'authMobile2', methods: ['POST'])]
    public function authMobile2(Request $request): JsonResponse
    {
        try {
            // Décodage du contenu de la requête
            $data = json_decode($request->getContent(), true);
    
            $mail = $data['mail'] ?? null;
            $mdp = $data['mdp'] ?? null; // Récupère l'ID de l'utilisateur depuis la requête
    
            if (!$mail || !$mdp) {
                // Log si le jeton ou l'ID de l'utilisateur sont manquants
                error_log("données manquantes dans la requête.");
    
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'données manquantes dans la requête.'
                ], 400);
            }
    
            // Log des valeurs reçues
            error_log("mail reçu: " . $mail);
            error_log("mdp reçu : " . $mdp);
    
            $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail]);
            if (!$utilisateur) {
                return $this->createErrorResponse(400, 'Aucun utilisateur associé à ce mail');
            }

            // Vérification du mot de passe
            if (!$this->authService->checkLogin($utilisateur, $mdp)) {
                return $this->createErrorResponse(400, 'Mot de passe incorrect');
            }

            $duree_jeton = $data['duree_jeton'] ?? -1;

            $jeton = new Jeton($duree_jeton);
            $this->entityManager->persist($jeton);
            $this->entityManager->flush();

            $jeton_authentification = new JetonAuthentification($utilisateur, $jeton);
            $this->entityManager->persist($jeton_authentification);
            $this->entityManager->flush();
            $userData = [
                'id' => $utilisateur->getId(),
                'mail' => utf8_encode($utilisateur->getMail()),
                'nom' => utf8_encode($utilisateur->getNom()),
                'mdp' => utf8_encode($utilisateur->getMdp()),
                'dtn' => $utilisateur->getDateNaissance()
            ];

            // Encodage JSON sécurisé avec UTF-8
            $test = json_encode($userData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            
            if ($test === false) {
                throw new \Exception('Erreur d\'encodage JSON: ' . json_last_error_msg());
            }

            // Réponse en cas de succès
            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'message' => 'Veuillez vérifier votre e-mail pour voir votre pin.',
                    'jeton' => $jeton->getJeton(),
                    'utilisateur' => $userData
                ]
            ], 200);

        } catch (\Exception $e) {
            // Gestion des erreurs internes
            return $this->createErrorResponse(500, $e->getMessage());
        }
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

            // Vérification de l'encodage des données
            if (!mb_check_encoding($data['mail'], 'UTF-8') || !mb_check_encoding($data['mdp'], 'UTF-8')) {
                return $this->createErrorResponse(400, 'Données mal encodées');
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

            $duree_jeton = $data['duree_jeton'] ?? -1;

            $jeton = new Jeton($duree_jeton);
            $this->entityManager->persist($jeton);
            $this->entityManager->flush();

            $jeton_authentification = new JetonAuthentification($utilisateur, $jeton);
            $this->entityManager->persist($jeton_authentification);
            $this->entityManager->flush();

            // Vérification de l'encodage des propriétés de l'utilisateur avant JSON
            $userData = [
                'id' => $utilisateur->getId(),
                'mail' => utf8_encode($utilisateur->getMail()),
                'nom' => utf8_encode($utilisateur->getNom()),
                'mdp' => utf8_encode($utilisateur->getMdp()),
                'dtn' => $utilisateur->getDateNaissance()
            ];

            // Encodage JSON sécurisé avec UTF-8
            $test = json_encode($userData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            
            if ($test === false) {
                throw new \Exception('Erreur d\'encodage JSON: ' . json_last_error_msg());
            }

            // Réponse en cas de succès
            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'message' => '',
                    'jeton' => $jeton->getJeton(),
                    'utilisateur' => $userData
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
