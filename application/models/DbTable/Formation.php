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
}
