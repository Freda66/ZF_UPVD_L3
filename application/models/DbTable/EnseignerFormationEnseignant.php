<?php

/**
 * Class d'access a la table EnseignerFormationEnseignant
 * @author Fred
 *
 */
class Application_Model_DbTable_EnseignerFormationEnseignant extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'enseignerformationenseignant';
	
    /**
     * Fonction qui renvoie la liste des formations enseigner par un enseignant
     * @param unknown $idEnseignant
     */
    public function getEnseignerFormation($idEnseignant){
    	// Prepare la requete
    	$result = $this	->select()
    					->where('idEnseignant = ?', $idEnseignant);
    	// Retourne le resultat de la requete
    	return $this->fetchAll($result);
    }
    
	/**
	 * Fonction qui insert une formation a un enseignant
	 * @param integer $idFormation
	 * @param integer $idEnseignant
	 * @return boolean
	 */
	public function insertEnseignantFormation($idFormation, $idEnseignant){
		try{
			if($this->insert(array('idFormation'=>$idFormation, 'idEnseignant'=>$idEnseignant))) return true;
			else return false;
		} catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * Fonction qui supprime les formations enseigner par un enseignant
	 * @param integer $idEnseignant
	 */
	public function deleteEnseignantFormation($idEnseignant){
		try {
			if($this->delete(array('idEnseignant = '.$idEnseignant))) return true; 
			else return false;
		} catch(Exception $e){ return false; }
	}
	
}
