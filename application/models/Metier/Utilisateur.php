<?php
/**
 * Class metier pour la connexion
 * @author Fred
 *
 */
class Application_Model_Metier_Utilisateur
{
	/**
	 * Connexion de l'utilisateur
	 * @param string $login : identifiant
	 * @param string $mdp : mot de passe
	 * @return bool : true (connecté), false (non connecté)
	 */
	public function connexionUser($login, $mdp)
	{
		// Bool qui indique que l'utilisateur a été trouvé
		$isFind = false;
		
		// Instancie les classes enseignant, etudiant et entreprise
		$modelEnseignant = new Application_Model_DbTable_Enseignant();
		$modelEtudiant = new Application_Model_DbTable_Etudiant();
		$modelEntreprise = new Application_Model_DbTable_Entreprise();
		
		// Recupere la session
		$session = Zend_Auth::getInstance()->getStorage()->read();
		
		// Test si c'est un enseignant
		if(!$isFind) {
			$isFind = $modelEnseignant->authentification($login, $mdp);		
			if($isFind){
				// Insert dans la session les informations de l'utilisateur
				$unEnseignant = $modelEnseignant->getEnseignantByLoginMdp($login, $mdp);
				$session->infoUser->type = "Enseignant";
				$session->infoUser->identifiant = $unEnseignant->idEnseignant;
				$session->infoUser->isResponsable = $unEnseignant->isResponsableSiteEnseignant;
			}
		}
		
		// Test si c'est un etudiant
		if(!$isFind) {
			$isFind = $modelEtudiant->authentification($login, $mdp);		
			if($isFind){
				// Insert dans la session les informations de l'utilisateur
				$unEtudiant = $modelEtudiant->getEtudiantByLoginMdp($login, $mdp);
				$session->infoUser->type = "Etudiant";
				$session->infoUser->identifiant = $unEtudiant->ineEtudiant;
			}
		}
		
		// Test si c'est une entreprise
		if(!$isFind) {
			$isFind = $modelEntreprise->authentification($login, $mdp);		
			if($isFind){
				// Insert dans la session les informations de l'utilisateur
				$uneEntreprise = $modelEntreprise->getEntrepriseByLoginMdp($login, $mdp);
				$session->infoUser->type = "Entreprise";
				$session->infoUser->identifiant = $uneEntreprise->idEntreprise;
			}
		}
		
		// Sauvegarde la session
		Zend_Auth::getInstance()->getStorage()->write($session);

		// Retourne le boolean
		return $isFind;
	}
}

