<?php

/**
 * Class d'access a la table Stage
 * @author Fred
 *
 */
class Application_Model_DbTable_Stage extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'stage';
	private $_nbItemByPage = 1;
	private $_nbPagePrint = 20;
    
    /**
     * Fonction qui retourne l'enregistrement d'un stage
     * @param integer $code
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
  	public function getStage($code, $session){
  		// Recupere les informations d'un stage
  		// Jointure sur personne pour recuperer le nom et prenom du tuteur de l'entreprise,...
  		$result = 	$this	->select()->setIntegrityCheck(false)
  							->from(array('s' => $this->_name), array('*'))
  							->joinLeft(array('p'=>'personne'), 's.idTuteur = p.idPersonne', array('*'))
  							->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idStage = s.codeStage', array('*'))
  							->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'))
  							->joinLeft(array('en'=>'enseignant'), 'res.idEnseignantTuteur = en.idEnseignant', array('*'))
  							->where('codeStage = ?', $code);
  		// Entreprise
  		if($session->type == "Entreprise") $result->where('idEntreprise = ?', $session->identifiant);
  		// Enseignant tuteur
  		if($session->type == "Enseignant" && !$session->isResponsable) $result->where('res.idEnseignantTuteur = ?', $session->identifiant);
  		
  		// Etudiant
  		if($session->type == "Etudiant") {
  			$result = $this->fetchAll($result);
  			
  			foreach($result as $unStage){
  				if($unStage->idEtudiant == $session->identifiant || $unStage->idEtudiant == null) return $unStage;
  			}
  			
  			// Pas de return avant donc return null
  			return null;  
  		} 
  		else {
  			$result = $this->fetchRow($result);
  		}
  		
  		// Retourne le resultat
  		return $result;
  	}
    
	/**
	 * Fonction qui retourne les stages d'une entreprise à partir d'un numéro de siret
	 * @param string $idEntreprise
	 * @param integer $formation
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getStagesEntreprise($idEntreprise, $idFormation, $page){
		// Requete qui recupere les stages d'un entreprise
		$result = $this	->select()->setIntegrityCheck(false);
		if($idFormation != null) {
  			$result		->from(array('s' => $this->_name), array('*'))
						->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
						->where('idFormation = ?', $idFormation);
		}
		$result			->where('idEntreprise = ?', $idEntreprise);

		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
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
	 * Retourne la liste des stages qui sont disponible (etat = 1) ou en attente (etat = 0)
	 * @param int $etat : 1 valid default, 0 en attente (responsable)
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getStagesAllValidORAttente($etat, $formation, $page){
		$result = $this	->select()->setIntegrityCheck(false)
  						->from(array('s' => $this->_name), array('*'))
						->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idStage = s.codeStage', array('*'));
		if($idFormation != null) {
			$result		->from(array('s' => $this->_name), array('*'))
						->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
						->where('idFormation = ?', $idFormation);
		}
		$result 		->where('etatStage = ?', $etat)
						->where('res.idStage is null');
		
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
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
	 * Retourne la liste des stages (enseignant responsable)
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getStages($page, $idFormation){
		// Requete qui recupere les stages d'un entreprise
		$result = $this->select()->setIntegrityCheck(false);
		if($idFormation != null) {
  			$result		->from(array('s' => $this->_name), array('*'))
  						->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
  						->where('idFormation = ?', $idFormation);
  		}
		
		// Retourne tout les stages
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
		// Détermine le nombre d'item par page
		$paginator ->setItemCountPerPage($this->_nbItemByPage);
		// Détermine la page en courrante
		$paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange($this->_nbPagePrint);
		// Retourne le resultat
		return $paginator;
	}
}
