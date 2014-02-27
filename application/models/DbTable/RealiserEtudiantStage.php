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
  	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
  	 */
  	public function getMyStages($ineEtudiant, $param){
  		$result = 	$this	->select()->setIntegrityCheck(false)
					  		->from(array('res' => $this->_name), array('*'))
					  		->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'))
					  		->where('idEtudiant = ?', $ineEtudiant);
  		// Filtre sur ses stages (avec un tuteur) => validé par le responsable
  		if($param == "stage") $result->where('idEnseignantTuteur is not null');
  		// Filtre sur ses demande de stage (sans enseignant tuteur renseigné => en attente de validation par le responsable
  		else if($param == "demande") $result->where('idEnseignantTuteur is null');
  		// Retourne le resultat sql
  		return $this->fetchAll($result)->toArray();
  	}
  	
  	/**
  	 * Fonction qui renvoie la liste des stages pour un enseignant tuteur
  	 * @param integer $idEnseignantTuteur
  	 */
  	public function getStagesTuteur($idEnseignantTuteur){
  		$result = 	$this	->select()->setIntegrityCheck(false)
					  		->from(array('res' => $this->_name), array('*'))
					  		->joinLeft(array('s'=>'stage'), 'res.idStage = s.codeStage', array('*'))
					  		->where('idEnseignantTuteur = ?', $idEnseignantTuteur);
  		// Retourne le resultat sql
  		return $this->fetchAll($result)->toArray();
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
}
