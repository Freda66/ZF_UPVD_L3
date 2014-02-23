<?php

/**
 * Class d'access a la table Etudiant
 * @author Fred
 *
 */
class Application_Model_DbTable_Etudiant extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'etudiant';

    /**
     * Fonction qui retourne les informations d'un etudiant
     * @param string $ine
     * @return Object, Array, NULL
     */
	public function getEtudiant($ine, $isArray = false)
	{
		// Cherche une ligne qui correspond a l'ine récuperé dans la bdd
		$result = $this->fetchRow('ineEtudiant = ' . $ine);
		// Si on ne la trouve pas on return null
		if($result) {
			if($isArray) return $result->toArray();
			else return $result;
		} else return null;
	}
	
	/**
	 * Fonction qui retourne les informations d'un etudiant
	 * @param string $login
	 * @param string $mdp
	 * @return Object
	 */
	public function getEtudiantByLoginMdp($login, $mdp)
	{
		// Crée un objet select
		$result = $this	->select()
						->where('loginEtudiant = ?', $login)
						->where('mdpEtudiant = ?', $mdp);
		// Retourne le resultat de la requete
		return $this->fetchRow($result);
	}
	
	/**
	 * Fonction qui connecte un etudiant au portail web
	 * @param string $login
	 * @param string $mdp
	 * @return boolean : (true => connecté, false => non connecté)
	 */
	public function authentification($login, $mdp)
	{
		// Crée un objet d'authentification a la bdd
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

		// Definit les valeurs de cette objet
		$authAdapter->setTableName('etudiant')
				  	->setIdentityColumn('loginEtudiant')
				  	->setCredentialColumn('mdpEtudiant');
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