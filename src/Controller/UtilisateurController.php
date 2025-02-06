<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Util\HasherUtil;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PHPMailer\PHPMailer\Exception;

class UtilisateurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository)
    {
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    #[Route('/utilisateur/modifier-nom', name: 'modifier_nom', methods: ['POST'])]
    public function modifierNom(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['id'], $data['nom'])) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Données manquantes.'
                ], 400);
            }

            $result = $this->utilisateurRepository->updateNom($data['id'], $data['nom']);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Nom modifié avec succès.'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #[Route('/utilisateur/modifier-mdp', name: 'modifier_mdp', methods: ['POST'])]
    public function modifierMdp(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['id'], $data['mdp'])) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Données manquantes.'
                ], 400);
            }

            $result = $this->utilisateurRepository->updateMdp($data['id'], $data['mdp']);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Mot de passe modifié avec succès.'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/utilisateur/modifier-date-naissance', name: 'modifier_date_naissance', methods: ['POST'])]
    public function modifierDateNaissance(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['id'], $data['dateNaissance'])) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Données manquantes.'
                ], 400);
            }

            try {
                $date = new \DateTime($data['dateNaissance']);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Format de date invalide.'
                ], 400);
            }

            $result = $this->utilisateurRepository->updateDateNaissance($data['id'], $date);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Date de naissance modifiée avec succès.'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    #[Route('/utilisateur/modifier-complet', name: 'modifier_complet', methods: ['POST'])]
    public function modifierComplet(Request $request): JsonResponse
{
    try {
        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier la présence des données nécessaires
        if (!isset($data['id'], $data['nom'], $data['mdp'], $data['dateNaissance'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Données manquantes.'
            ], 400);
        }

        // Trouver l'utilisateur dans la base de données
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find($data['id']);

        if (!$utilisateur) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }

        // Mettre à jour les champs de l'utilisateur
        $utilisateur->setNom($data['nom']);

        $hashedPassword = HasherUtil::hashPassword($data['mdp']);

        // Hachage du mot de passe avant sauvegarde
        $utilisateur->setMdp($hashedPassword);

        try {
            $date = new \DateTime($data['dateNaissance']);
            $utilisateur->setDateNaissance($date);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Format de date invalide.'
            ], 400);
        }

        // Persister et sauvegarder les modifications
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Utilisateur mis à jour avec succès.'
        ]);

    } catch (\Exception $e) {
        return new JsonResponse([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
}