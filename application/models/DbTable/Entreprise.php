<?php

/**
 * Class d'access a la table Entreprise
 * @author Fred
 *
 */
class Application_Model_DbTable_Entreprise extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'entreprise';

    /**
     * Fonction qui retourne les informations d'une entreprise
     * @param string $siret
     * @return Object, Array, NULL
     */
	public function getEntreprise($siret, $isArray = false)
	{
		// Cherche une ligne qui correspond au siret récuperé dans la bdd
		$result = $this->fetchRow('siretEntreprise = ' . $siret);
		// Si on ne la trouve pas on return null
		if($result) {
			if($isArray) return $result->toArray();
			else return $result;
		} else return null;
	}
	
	/**
	 * Fonction qui retourne les informations d'une entreprise
	 * @param string $login
	 * @param string $mdp
	 * @return Object
	 */
	public function getEntrepriseByLoginMdp($login, $mdp)
	{
		// Crée un objet select
		$result = $this	->select()
						->where('loginEntreprise = ?', $login)
						->where('mdpEntreprise = ?', $mdp);
		// Retourne le resultat de la requete
		return $this->fetchRow($result);
	}
	
	/**
	 * Fonction qui connecte une Entreprise au portail web
	 * @param string $login
	 * @param string $mdp
	 * @return boolean : (true => connecté, false => non connecté)
	 */
	public function authentification($login, $mdp)
	{
		// Crée un objet d'authentification a la bdd
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

		// Definit les valeurs de cette objet
		$authAdapter->setTableName('entreprise')
				  	->setIdentityColumn('loginEntreprise')
				  	->setCredentialColumn('mdpEntreprise');
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
