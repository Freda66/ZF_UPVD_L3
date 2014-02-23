<?php

/**
 * Class d'access a la table Enseignant
 * @author Fred
 *
 */
class Application_Model_DbTable_Enseignant extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'enseignant';

    /**
     * Fonction qui retourne les informations d'un Enseignant
     * @param string $id
     * @return Object, Array, NULL
     */
	public function getEnseignant($id, $isArray = false)
	{
		// Cherche une ligne qui correspond a l'id récuperé dans la bdd
		$result = $this->fetchRow('idEnseignant = ' . $ine);
		// Si on ne la trouve pas on return null
		if($result) {
			if($isArray) return $result->toArray();
			else return $result;
		} else return null;
	}
	
	/**
	 * Fonction qui retourne les informations d'un Enseignant
	 * @param string $login
	 * @param string $mdp
	 * @return Object
	 */
	public function getEnseignantByLoginMdp($login, $mdp)
	{
		// Crée un objet select
		$result = $this	->select()
						->where('loginEnseignant = ?', $login)
						->where('mdpEnseignant = ?', $mdp);
		// Retourne le resultat de la requete
		return $this->fetchRow($result);
	}
	
	/**
	 * Fonction qui connecte un Enseignant au portail web
	 * @param string $login
	 * @param string $mdp
	 * @return boolean : (true => connecté, false => non connecté)
	 */
	public function authentification($login, $mdp)
	{
		// Crée un objet d'authentification a la bdd
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

		// Definit les valeurs de cette objet
		$authAdapter->setTableName('enseignant')
				  	->setIdentityColumn('loginEnseignant')
				  	->setCredentialColumn('mdpEnseignant');
					//$authAdapter->setCredentialTreatment("MD5(?)"); // Cryptage MD5
		$authAdapter->setIdentity($login)
				  	->setCredential($mdp);

		// Authentification
    	$result = $authAdapter->authenticate();

		// Vérifie que le résultat est valide
		if($result->isValid()) return true;
    	else return false;	
	}
		
}
