<?php

/**
 * Class d'access a la table DemandeEtudiantStage
 * @author Fred
 *
 */
class Application_Model_DbTable_DemandeEtudiantStage extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'demandeetudiantstage';
    private $_nbItemByPage = 5;
    private $_nbPagePrint = 20;
	
	/**
	 * Retourne la liste des demande d'un stage
	 * @param integer $idStage
	 * @return Zend_Paginator
	 */
	public function getListeDemandeStage($idStage){
		// Crée un objet select
		$requete = 	$this	->select()->setIntegrityCheck(false)
							->from(array('des' => $this->_name), array('*'))
							->joinLeft(array('e'=>'etudiant'), 'des.idEtudiant = e.ineEtudiant', array('*'))
							->where('idStage = ?', (int)$idStage);
		// Retourne le resultat de la recherche
		return $this->fetchAll($requete);		
	}
	
	/**
	 * Insert une demande a un stage dans la table
	 * @param integer $idEtudiant
	 * @param integer $idStage
	 */
	public function insertDemandeStage($idStage, $idEtudiant){
		try{
			if($this->insert(array('idEtudiant'=>$idEtudiant, 'idStage'=>$idStage))) return true;
			else return false;
		} catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Fonction qui supprime les demande pour un stage
	 * @param integer $idStage
	 */
	public function deleteDES($idStage){
		if($this->delete(array('idStage = '.(int)$idStage))) return true;
		else return false;
	}
	
	/**
	 * Fonction qui retourne les stages demandés d'un etudiant
	 * @param integer $ineEtudiant
	 * @param integer $page
	 * @return Zend_Paginator
	 */
	public function getMyStages($ineEtudiant, $idFormation, $page){
		$result = 	$this	->select()->setIntegrityCheck(false)
							->from(array('des' => $this->_name), array('*'))
							->joinLeft(array('s'=>'stage'), 'des.idStage = s.codeStage', array('*'));
		if($idFormation != null) {
			$result			->joinLeft(array('cfs'=>'concernerformationstage'), 'cfs.idStage = s.codeStage', array('*'))
							->where('idFormation = ?', $idFormation);
		}
		$result		  		->where('idEtudiant = ?', $ineEtudiant);
	
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

}
