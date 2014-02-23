<?php

class Application_Model_DbTable_LigneCommande extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'ligne_cde';
	
	public function setLigneCommande($numCommande, $idProduit, $qteProduit, $prixProduit, $typeVente)
	{
		// Prepare la requete
		$data = array('id_cde'=>$numCommande, 'id_produit'=>$idProduit, 'qte_produit'=>$qteProduit, 'prix_produit'=>$prixProduit, 'type_vente'=>$typeVente, 'estimation_produit'=>0);
		$this->insert($data);

		return true;
	}
	
	// Ajoute une estimation
	public function setEstimationProduit($idLigneCde, $etat)
	{
		// Recupere l'id du membre 
		$membre = new Application_Model_DbTable_Membre();
		$membre = $membre->getMembreAuth();
		
		// Prepare la requete
		$record = $this->select()->setIntegrityCheck(false)
			->from(array('lc' => $this->_name), array('*'))
			->joinLeft(array('c' => 'commande'), 'c.id_cde = lc.id_cde', array('*'))
			->where('c.id_membre = ?', $membre->id)
			->where('lc.id_ligne_cde = ?', $idLigneCde);
		// Execute la requete
		$record = $this->fetchRow($record);
		$result = false;
		if($record->estimation_produit == 0){ $result = true; }
		
		// Verifie que la ligne commande appartienne au membre authentifié
		if($result)
		{
			// Crée un objet produit
			$produit = new Application_Model_DbTable_Produit();
			$result = $produit->setTopClassement($record->id_produit, $etat);
			// Verifie que l'estimation a été ajoutée
			if($result)
			{
				// Prepare la requete
				$data = array('estimation_produit'=>1);
				// Modification
				$this->update($data, 'id_ligne_cde = '. (int)$idLigneCde);
			}
		}
				
		return $result;
	}
	
	/**
	 * Supprime toute les lignes d'une commande
	 * @param integer $idCommande
	 */
	public function deleteCommande($idCommande){
		// Recupere les lignes d'une commande
		$result = $this	->select()
						->where('id_cde = ?', $idCommande);
		$lesLignes = $this->fetchAll($result);

		// Cr�e un objet db table produit
		$objectProduit = new Application_Model_DbTable_Produit();
		// Remet la quantit� au produit
		foreach($lesLignes as $uneLigne){
			$objectProduit->deleteQteProduit($uneLigne->id_produit, $uneLigne->qte_produit, false);
			$objectProduit->setTopVente($uneLigne->id_produit, $uneLigne->qte_produit, false);
		}
		
		// Supprime les lignes de la commande
		$this->delete('id_cde='.(int)$idCommande);
	}
	
}

