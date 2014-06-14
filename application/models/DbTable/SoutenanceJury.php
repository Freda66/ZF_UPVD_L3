<?php

/**
 * Class d'access a la table SoutenanceJury
 * @author Fred
 *
 */
class Application_Model_DbTable_SoutenanceJury extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'soutenancejury';

    /**
     * Fonction qui retourne la liste de la composition d'une soutenance
     * @param integer $idSoutenance
     * @return Object, NULL
     */
    public function getSoutenanceJury($idSoutenance)
    {
    	$result = 	$this	->select()->setIntegrityCheck(false)
					    	->from(array('sj' => $this->_name), array('*'))
					    	->joinLeft(array('p'=>'personne'), 'sj.idPersonne = p.idPersonne', array('*'))
					    	->joinLeft(array('en'=>'enseignant'), 'sj.idEnseignant = en.idEnseignant', array('*'))
					    	->where('codeSoutenance = ?', $idSoutenance);
    	return $this->fetchAll($result);
    }
    
    /**
     * Insert une composition soutenance jury dans la bdd
     * @param integer $idSoutenance
     * @param integer $idPersonne
     * @param integer $idEnseignant
     * @return boolean
     */
    public function insertSoutenanceJury($idSoutenance, $idPersonne, $idEnseignant){
    	try{
    		if($this->insert(array('codeSoutenance'=>$idSoutenance, 'idPersonne'=>$idPersonne, 'idEnseignant'=>$idEnseignant))) return true;
    		else return false;
    	} catch(Exception $e){
    		return false;
    	}
    }
    
    /**
     * Supprime une composition soutenance jury de la bdd
     * @param integer $idSoutenance
     * @param integer $idPersonne
     * @param integer $idEnseignant
     * @return boolean
     */
    public function deleteSoutenance($idSoutenance, $idPersonne, $idEnseignant){
    	try{
    		if($idPersonne == null) {
    			if($this->delete(array('codeSoutenance = '.(int)$idSoutenance, 'idEnseignant = '.$idEnseignant))) return true;
    			else return false;
    		} else {
	    		if($this->delete(array('codeSoutenance = '.(int)$idSoutenance, 'idPersonne = '.$idPersonne))) return true;
	    		else return false;
    		}
    	} catch (Exception $e) { echo $e; exit; return false; }
    }
    
}
