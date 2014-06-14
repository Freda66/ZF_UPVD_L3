<?php

/**
 * Class d'access a la table Soutenance
 * @author Fred
 *
 */
class Application_Model_DbTable_Soutenance extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'soutenance';
	private $_nbItemByPage = 5;
	private $_nbPagePrint = 20;
    
	/**
	 * Retourne la liste des soutenances (enseignant responsable)
	 * @param integer $page
	 * @param integer $idFormation
	 * @return Zend_Paginator
	 */
	public function getSoutenances($page, $idFormation, $session, $tuteur){
		// Requete qui recupere les soutenances de la bdd
		$result = $this	->select()->setIntegrityCheck(false);
			$result		->from(array('so' => $this->_name), array('*'))
						->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idSoutenance= so.idSoutenance', array('*'))
						->joinLeft(array('st'=>'stage'), 'st.codeStage = res.idStage', array('*'));
		// Formation
		if($idFormation != null) {
			$result		->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = st.codeStage', array('*'))
						->where('cfs.idFormation = ?', (int)$idFormation);
		}

		// Enseignant tuteur
		if($session->type == "Enseignant" && $tuteur == "tuteur") $result->where('res.idEnseignantTuteur = ?', (int)$session->identifiant);
		// Etudiant
		if($session->type == "Etudiant") 	$result->where('res.idEtudiant = ?', $session->identifiant);
		//Entreprise
		if($session->type == "Entreprise") 	$result->where('st.idEntreprise = ?', $session->identifiant);
		
		// Stage actif uniquement
		$result			->where('etatStage = 1');
		
		// Retourne tout les soutenances
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
	 * Recupere les informations d'une soutenance
	 * @param integer $idSoutenance
	 * @param session $session
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	public function getSoutenanceById($idSoutenance, $session){
		// Requete qui recupere les informations d'un soutenance avec la fiche du stage
		$result = $this	->select()->setIntegrityCheck(false);
		$result			->from(array('so' => $this->_name), array('*'))
						->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idSoutenance= so.idSoutenance', array('*'))
						->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'))
						->joinLeft(array('st'=>'stage'), 'st.codeStage = res.idStage', array('*'))
						->joinLeft(array('p'=>'personne'), 'st.idTuteur = p.idPersonne', array('*'))
						->joinLeft(array('en'=>'enseignant'), 'res.idEnseignantTuteur = en.idEnseignant', array('*'))
						->where('so.idSoutenance = ?', (int)$idSoutenance);
		// Entreprise
		if($session->type == "Entreprise") $result->where('idEntreprise = ?', $session->identifiant);
		// Enseignant tuteur
		if($session->type == "Enseignant" && !$session->isResponsable) $result->where('res.idEnseignantTuteur = ?', $session->identifiant);
		// Etudiant
		if($session->type == "Etudiant") 	$result->where('res.idEtudiant = ?', $session->identifiant);
		
		return $this->fetchRow($result);
	}
	
	/**
	 * Retourne les informations minimum d'une soutenance
	 * @param integer $idSoutenance
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	public function getSoutenanceForm($idSoutenance){
		$result = $this	->select()->setIntegrityCheck(false);
		$result			->from(array('so' => $this->_name), array('*'))
						->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idSoutenance = so.idSoutenance', array('*'))
						->where('so.idSoutenance = ?', (int)$idSoutenance);
		return $this->fetchRow($result);
	}
	
	/**
	 * Insert une soutenance
	 * @param integer $date
	 * @param integer $salle
	 * @return boolean
	 */
	public function insertSoutenance($date, $salle){
		try{
			// Crée un ligne enseignant
			$row = $this->createRow();
			// Prepare les colonnes de la ligne
			$row->dateSoutenance = $date;
			$row->salleSoutenance = $salle;
			// Insert la ligne dans la bdd et retourne son id
			return $row->save();
		} catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Modification d'une soutenance
	 * @param integer $idSoutenance
	 * @param string $dateSoutenance
	 * @param string $salleSoutenance
	 * @return boolean
	 */
	public function updateSoutenance($idSoutenance, $dateSoutenance, $salleSoutenance){
		try {
			// Param + Update
			$data = array('dateSoutenance'=>$dateSoutenance, 'salleSoutenance'=>$salleSoutenance);
			$this->update($data, 'idSoutenance = '. (int)$idSoutenance);
			return true;
		} catch(Exeception $e) { return false; }
		 
	}
	
	/**
	 * Supprime une soutenance
	 * @param integer $idSoutenance
	 * @return boolean
	 */
	public function deleteSoutenance($idSoutenance)
	{
		if($this->delete(array('idSoutenance = '.$idSoutenance))) return true;
		else return false;
	}
	
}
