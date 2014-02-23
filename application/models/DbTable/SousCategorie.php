<?php

class Application_Model_DbTable_SousCategorie extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'souscategorie';

	public function getSousCategorie($idSousCategorie)
	{
		// Creé un object select
		$result = $this->select();
		// Prepare la requete
		$result -> where('idSousCategorie = ?', $idSousCategorie);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Retourne le resultat de la requete
		return $result->libelleSousCategorie;				
	}
	
}

