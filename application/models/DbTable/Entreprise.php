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
	private $_nbItemByPage = 5;
	private $_nbPagePrint = 20;

    /**
     * Fonction qui retourne les informations d'une entreprise
     * @param integer $id
     * @return Object, Array, NULL
     */
	public function getEntreprise($id, $isArray = false)
	{
		$result = 	$this	->select()->setIntegrityCheck(false)
							->from(array('e' => $this->_name), array('*'))
							->joinLeft(array('p'=>'personne'), 'e.idPersonneDirigeant = p.idPersonne', array('*'))
							->where('idEntreprise = ?', $id);
		$result = $this->fetchRow($result);
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

		// Récupérer l'objet select (par référence) 
		$select = $authAdapter->getDbSelect();
		$select->where('etatEntreprise = 1'); // Test que l'utilisateur soit actif

		// Authentification
    	$result = $authAdapter->authenticate();

		// Vérifie que le résultat est valide
		if($result->isValid()) return true;
    	else return false;	
	}
	
	/**
	 * Retourne sous pagination la liste des entreprise du site
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getListeEntreprise($page){
		// Liste des entreprise actif
		$requete = $this->select()->where('etatEntreprise = ?', 1);
		
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($requete));
		// Détermine le nombre d'item par page
		$paginator ->setItemCountPerPage($this->_nbItemByPage);
		// Détermine la page en courrante
		$paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange($this->_nbPagePrint);
		// Retourne le resultat
		return $paginator;
	}
	
	/**
	 * Fonction qui supprime l'entreprise de la table
	 * Si exception (dépendance dans une autre table) on passe sont etat a -1
	 * @param integer $idEntreprise
	 */
	public function deleteEntreprise($idEntreprise){
		try {
			// Supprime l'entreprise
			$this->delete('idEntreprise = '.$idEntreprise);
		}catch (Exception $e){
			// Si une erreur est déclanché (dépendance de clé étrangere), on passe sont etat a -1
			$this->update(array('etatEntreprise'=>-1), 'idEntreprise = '.$idEntreprise);
		}
	}
}
