<?php
namespace App\Service;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Util\HasherUtil;



class AuthService 
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public  function checkLogin(Utilisateur $utilisateur, $plainPassword ): bool
    {
       if(HasherUtil::verifyPassword($plainPassword,$utilisateur->getMdp()))
       {
            return true;
       }
       else{
        return false;
       }

    }


    public function estEncoreConnecte(string $utilisateur): bool
    {
        // Récupérer le dernier jeton d'authentification
        $jetonAuth = $this->jetonAuthentificationRepository->findOneBy(
            ['utilisateur' => $utilisateur],
            ['dateCreation' => 'DESC'] // Trié du plus récent au plus ancien
        );

        if (!$jetonAuth) {
            return false; // Aucun jeton trouvé, donc non valide
        }

        // Vérifier si le jeton est expiré
        return !$jetonAuth->isExpired(); // Renvoie true si valide, false si expiré
    }
}
