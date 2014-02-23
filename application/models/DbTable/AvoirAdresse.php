<?php

class Application_Model_DbTable_AvoirAdresse extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'avoir';

	// Ajoute une ligne dans la table avoir
	public function addAvoir($idMembre, $idAdr)
	{
		// Récupere dans un tableau les valeurs de la ligne a inserer
		$data = array('id_membre'=>$idMembre,'id_adr_avoir'=>$idAdr);
		// Insert la ligne dans la bdd
		$this->insert($data);
	}
	
	// Supprimer une ligne de la table avoir
	public function deleteAvoir($idMembre, $idAdr)
	{
		$this->delete(array('id_membre ='.$idMembre,'id_adr_avoir ='.$idAdr));
	}

}

