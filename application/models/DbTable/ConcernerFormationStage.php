<?php

/**
 * Class d'access a la table ConcernerFormationStage
 * @author Fred
 *
 */
class Application_Model_DbTable_ConcernerFormationStage extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'concernerformationstage';
	
	/**
	 * Retourne la liste des formation d'un stage
	 * @param integer $idStage
	 * @return Zend_Paginator
	 */
	public function getListeFormationStage($idStage){
		// Crée un objet select
		$requete = $this->select()->where('idStage = ?', (int)$idStage);
		// Retourne le resultat de la recherche
		return $this->fetchAll($requete);		
	}
	
	/**
	 * Insert une formation a un stage dans la table
	 * @param integer $idFormation
	 * @param integer $idStage
	 */
	public function insertFormationStage($idFormation, $idStage){
		try{
			if($this->insert(array('idFormation'=>$idFormation, 'idStage'=>$idStage))) return true;
			else return false;
		} catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Fonction qui supprime les formations pour un stage
	 * @param integer $codeStage
	 */
	public function deleteCFS($codeStage){
		if($this->delete(array('idStage = '.$codeStage))) return true;
		else return false;
	}

}
