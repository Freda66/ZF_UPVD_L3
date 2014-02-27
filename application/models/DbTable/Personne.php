<?php

/**
 * Class d'access a la table Personne
 * @author Fred
 *
 */
class Application_Model_DbTable_Personne extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'personne';

	/**
	 * Fonction qui retourne la liste des tuteurs d'un entreprise
	 * @param integer $idEntreprise
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getPersonneByEntreprise($idEntreprise)
	{
		// Crée un objet select
		$result = $this	->select()
						->where('idEntrepriseTravail = ?', $idEntreprise);
		// Retourne le resultat de la requete
		return $this->fetchAll($result);
	}
		
}
