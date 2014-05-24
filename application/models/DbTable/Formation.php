<?php

/**
 * Class d'access a la table Formation
 * @author Fred
 *
 */
class Application_Model_DbTable_Formation extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'formation';

    /**
     * Fonction qui retourne la liste entiere des formations
     */
    public function getFormations(){
    	return $this->fetchAll();
    }
    
    /**
     * Fonction qui retourne les informations d'une Formation
     * @param integer $id
     * @return Object, Array, NULL
     */
    public function getFormation($id, $isArray = false)
    {
    	// Cherche une ligne qui correspond a l'id récuperé dans la bdd
    	$result = $this->fetchRow('codeFormation = ' . $id);
    	// Si on ne la trouve pas on return null
    	if($result) {
    		if($isArray) return $result->toArray();
    		else return $result;
    	} else return null;
    }
}
