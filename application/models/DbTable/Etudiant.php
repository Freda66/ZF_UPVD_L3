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
	private $_nbItemByPage = 5;
	private $_nbPagePrint = 20;

    /**
     * Fonction qui retourne les informations d'un etudiant
     * @param string $ine
     * @return Object, Array, NULL
     */
	public function getEtudiant($ine, $isArray = false)
	{
		// Recupere les informations d'un etudiant
		$result = 	$this	->select()->setIntegrityCheck(false)
							->from(array('e' => $this->_name), array('*'))
							->joinLeft(array('f'=>'formation'), 'e.idFormation = f.codeFormation', array('*'))
							->where('ineEtudiant = ?', $ine);
		$result = $this->fetchRow($result);
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

		// Récupérer l'objet select (par référence) 
		$select = $authAdapter->getDbSelect();
		$select->where('etatEtudiant = 1'); // Test que l'utilisateur soit actif
		
		// Authentification
    	$result = $authAdapter->authenticate();

		// Vérifie que le résultat est valide
		if($result->isValid()) return true;
    	else return false;	
	}
	
	/**
	 * Retourne sous pagination la liste des etudiants du site
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getListeEtudiant($page){
		// Liste des etudiants actif
		$requete = $this->select()->where('etatEtudiant = ?', 1);
		
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
	 * Fonction qui supprime l'etudiant de la table
	 * Si exception (dépendance dans une autre table) on passe sont etat a -1
	 * @param integer $idEtudiant
	 */
	public function deleteEtudiant($ineEtudiant){
		try {
			// Supprime l'etudiant
			$this->delete("ineEtudiant = '$ineEtudiant'");
		}catch (Exception $e){
			// Si une erreur est déclanché (dépendance de clé étrangere), on passe sont etat a -1
			$this->update(array('etatEtudiant'=>-1), "ineEtudiant = '$ineEtudiant'");
		}
	}
		
}
