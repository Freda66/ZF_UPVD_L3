<?php

/**
 * Class d'access a la table RealiserEtudiantStage
 * @author Fred
 *
 */
class Application_Model_DbTable_RealiserEtudiantStage extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'realiseretudiantstage';
    private $_nbItemByPage = 1;
    private $_nbPagePrint = 20;
    
	/**
	 * Fonction qui retourne les informations d'un stage qui va etre realisé
	 * @param integer $codeStage
	 * @param integer $ineEtudiant
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
  	public function getRealiserEtudiantStage($codeStage, $ineEtudiant){
  		$result = 	$this	->select()->setIntegrityCheck(false)
  							->from(array('res' => $this->_name), array('*'))
  							->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'))
  							->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'))
  							->where('idStage = ?', $codeStage)
  							->where('idEtudiant = ?', $ineEtudiant);
  		return $this->fetchRow($result);
  	}
  	
  	/**
  	 * Fonction qui retourne les stages d'un etudiant
  	 * @param integer $ineEtudiant
  	 * @param string $param
  	 * @param integer $page
  	 * @return Zend_Paginator
  	 */
  	public function getMyStages($ineEtudiant, $param, $idFormation, $page){
  		$result = 	$this	->select()->setIntegrityCheck(false)
					  		->from(array('res' => $this->_name), array('*'))
					  		->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'));
  		if($idFormation != null) {
  			$result			->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
  							->where('idFormation = ?', $idFormation);
  		}
		$result		  		->where('idEtudiant = ?', $ineEtudiant);
  		// Filtre sur ses stages (avec un tuteur) => validé par le responsable
  		if($param == "stage") $result->where('idEnseignantTuteur is not null');
  		// Filtre sur ses demande de stage (sans enseignant tuteur renseigné => en attente de validation par le responsable
  		else if($param == "demande") $result->where('idEnseignantTuteur is null');

  		if($page == null){
  			return $this->fetchAll($result);
  		} else {
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
  	
  	/**
  	 * Fonction qui renvoie la liste des stages pour un enseignant tuteur
  	 * @param integer $idEnseignantTuteur
  	 * @param integer $page
  	 * @return Zend_Paginator
  	 */
  	public function getStagesTuteur($idEnseignantTuteur, $idFormation, $page){
  		$result = 	$this	->select()->setIntegrityCheck(false)
					  		->from(array('res' => $this->_name), array('*'))
					  		->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'))
  							->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'));
  		if($idFormation != null) {
  			$result		->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
  						->where('idFormation = ?', $idFormation);
  		}
		$result		  	->where('idEnseignantTuteur = ?', $idEnseignantTuteur);
		
		if($page == null){
			return $this->fetchAll($result);
		} else {
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
  	
  	/**
  	 * Fonction qui renvoie la liste des stages pour une entreprise avec le détails (edutiant, tuteur enseignant)
  	 * @param integer $idEntreprise
  	 * @return Zend_Paginator
  	 */
  	public function getStagesEntreprise($idEntreprise){
  		$result = 	$this	->select()->setIntegrityCheck(false)
	  		->from(array('res' => $this->_name), array('*'))
	  		->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'))
	  		->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'))
  			->where('s.idEntreprise = ?', $idEntreprise);
  	
  		return $this->fetchAll($result);
  	}
  	    
	/**
	 * Fonction qui si le stage existe dans la table
	 * @param integer $codeStage
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function isExist($codeStage){
		// Requete qui recupere les stages d'un entreprise
		$result = $this	->select()
						->where('idStage = ?', $codeStage)
						->where('idEnseignantTuteur is not null');
		// Retourne true si le resultat existe					
		if($this->fetchRow($result) != null) return true;
		else return false;
	}
	
	/**
	 * Fonction qui insert la demande de stage d'un etudiant dans la table
	 * @param integer $codeEtudiant
	 * @param integer $codeStage
	 * @return boolean
	 */
	public function insertRES($codeEtudiant, $codeStage){
		try{
			if($this->insert(array('idEtudiant'=>$codeEtudiant, 'idStage'=>$codeStage))) return true;
			else return false;
		} catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Fonction qui supprime la demande de stage d'un etudiant 
	 * @param integer $codeStage
	 * @param integer $codeEtudiant : -1 si enseignant responsable
	 */
	public function deleteRES($codeStage, $codeEtudiant = -1){
		if($codeEtudiant == -1) {
			if($this->delete(array('idStage = '.$codeStage))) return true;
			else return false;
		} else {
			if($this->delete(array('idStage = '.$codeStage, 'idEtudiant = "'.$codeEtudiant.'"', 'idEnseignantTuteur is null'))) return true;
			else return false;
		}
	}
	
	/**
	 * Fonction qui supprime la demande de stage d'un etudiant
	 * @param integer $codeStage
	 * @param integer $codeEtudiant : -1 si enseignant responsable
	 */
	public function deleteRESByResponsable($codeStage, $codeEtudiant = -1){
		try {
			if($codeEtudiant == -1) $this->delete(array('idStage = '.$codeStage));
			else $this->delete(array('idStage = '.$codeStage, 'idEtudiant = "'.$codeEtudiant.'"'));
			return true;
		} catch(Exception $e){ return false; }
	}
	
	/**
	 * Fonction qui retire un enseignant tuteur
	 * @param integer $codeStage
	 * @param integer $codeEtudiant
	 * @return boolean
	 */
	public function retirerenseignant($codeStage){
		try {
			$data = array('idEnseignantTuteur'=>NULL);
			if($this->update($data, 'idStage = '.(int)$codeStage)) return true;
			else return false;
		} catch(Exeception $e) { return false; }
	}
}
