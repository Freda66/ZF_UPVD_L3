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
	private $_nbItemByPage = 5;
	private $_nbPagePrint = 20;

    /**
     * Fonction qui retourne les informations d'un Enseignant
     * @param integer $id
     * @return Object, Array, NULL
     */
	public function getEnseignant($id, $isArray = false)
	{
		// Cherche une ligne qui correspond a l'id récuperé dans la bdd
		$result = $this->fetchRow('idEnseignant = ' . $id);
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

		// Récupérer l'objet select (par référence)
		$select = $authAdapter->getDbSelect();
		$select->where('etatEnseignant = 1'); // Test que l'utilisateur soit actif
		
		// Authentification
    	$result = $authAdapter->authenticate();

		// Vérifie que le résultat est valide
		if($result->isValid()) return true;
    	else return false;	
	}
	
	/**
	 * Retourne sous pagination la liste des enseignants du site
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getListeEnseignant($page){
		// Liste des enseignants actif
		$requete = $this->select()->where('etatEnseignant = ?', 1);
		
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
	 * Fonction qui supprime l'enseignant de la table
	 * Si exception (dépendance dans une autre table) on passe sont etat a -1
	 * @param integer $idEnseignant
	 */
	public function deleteEnseignant($idEnseignant){
		try {
			// Supprime l'enseignant
			$this->delete('idEnseignant = '.$idEnseignant);
		}catch (Exception $e){
			// Si une erreur est déclanché (dépendance de clé étrangere), on passe sont etat a -1
			$this->update(array('etatEnseignant'=>-1), 'idEnseignant = '.$idEnseignant);
		}
	}
	

	/**
	 * Fonction qui insert un enseignant dans la bdd
	 * @param string $nomEnseignant
	 * @param string $prenomEnseignant
	 * @param interger $fonctionEnseignant
	 * @param interger $specialiteEnseignant
	 * @param string $loginEnseignant
	 * @param string $mdpEnseignant
	 * @param integer $isResponsableSiteEnseignant
	 * @return integer
	 */
	public function insertEnseignant($nomEnseignant, $prenomEnseignant, $fonctionEnseignant, $specialiteEnseignant, $loginEnseignant, $mdpEnseignant, $isResponsableSiteEnseignant){
		try {
			// Crée un ligne enseignant
			$row = $this->createRow();
			// Prepare les colonnes de la ligne
			$row->nomEnseignant = $nomEnseignant;
			$row->prenomEnseignant = $prenomEnseignant;
			$row->fonctionEnseignant = $fonctionEnseignant;
			$row->specialiteEnseignant = $specialiteEnseignant;
			$row->loginEnseignant = $loginEnseignant;
			$row->mdpEnseignant = $mdpEnseignant;
			$row->isResponsableSiteEnseignant = $isResponsableSiteEnseignant;
			$row->etatEnseignant = 1;

			// Insert la ligne dans la bdd et retourne son id
			return $row->save();
		} catch(Exeception $e) { return -1; }
	}
	
	/**
	 * Fonction qui modifie un enseignant
	 * @param unknown $nomEnseignant
	 * @param unknown $prenomEnseignant
	 * @param unknown $fonctionEnseignant
	 * @param unknown $specialiteEnseignant
	 * @param unknown $loginEnseignant
	 * @param unknown $mdpEnseignant
	 * @param unknown $isResponsableSiteEnseignant
	 * @param unknown $codeEnseignant
	 * @return boolean
	 */
	public function updateEnseignant($nomEnseignant, $prenomEnseignant, $fonctionEnseignant, $specialiteEnseignant, $loginEnseignant, $mdpEnseignant, $isResponsableSiteEnseignant, $codeEnseignant){
		try {
			// Param
			if($mdpEnseignant != "") $data = array('nomEnseignant'=>$nomEnseignant, 'prenomEnseignant'=>$prenomEnseignant,'fonctionEnseignant'=>$fonctionEnseignant,'specialiteEnseignant'=>$specialiteEnseignant,'loginEnseignant'=>$loginEnseignant,'mdpEnseignant'=>$mdpEnseignant, 'isResponsableSiteEnseignant'=>$isResponsableSiteEnseignant);
			else $data = array('nomEnseignant'=>$nomEnseignant, 'prenomEnseignant'=>$prenomEnseignant,'fonctionEnseignant'=>$fonctionEnseignant,'specialiteEnseignant'=>$specialiteEnseignant,'loginEnseignant'=>$loginEnseignant, 'isResponsableSiteEnseignant'=>$isResponsableSiteEnseignant);
			// Update
			$this->update($data, 'idEnseignant = '. (int)$codeEnseignant); 
			return true;
		} catch(Exeception $e) { return false; }
	}
		
}
