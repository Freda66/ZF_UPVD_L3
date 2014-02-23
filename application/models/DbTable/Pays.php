<?php

class Application_Model_DbTable_Pays extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'pays';
	
	// Recupere les pays
	public function getPays()
	{
		// Crée un objet select
		$result = $this->select();
		// Execute la requete
		$result = $this->fetchAll($result);
		// Retourne toutes les valeurs correspondantes
		return $result;
	}
	
	// Recupere un pays
	public function getPaysById($idPays)
	{
		// Crée un objet select
		$result = $this->select()
				-> where('id_pays = ?', $idPays);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Retourne le pays
		return $result;
	}
	
	// Recupere un pays
	public function getPaysByEn($pays)
	{
		// Crée un objet select
		$result = $this->select()
		-> where('en = ?', $pays);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Retourne l'id du pays
		return $result->id_pays;
	}
	
}

