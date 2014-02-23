<?php

class Application_Model_DbTable_Commande extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'commande';

	/**
	 * Ajoute une commande (adresse, commande, avoirAdresse)
	 * Retourne l'id de la commande
	 * @param integer $idMembre
	 * @param array $adressePanier
	 * @return integer
	 */
	public function setCommande($idMembre, $adressePanier, $typePaiement)
	{
		// INSERT LES ADRESSES
		$uneAdresse = new Application_Model_DbTable_Adresse();
		$unMembreAvoirAdresse = new Application_Model_DbTable_AvoirAdresse();
		// Verifie si l'adresse de livraison existe deja
		$adresseLivraison = $uneAdresse->checkAdresse($adressePanier['nom_adresse'][0], $adressePanier['prenom_adresse'][0], $adressePanier['civilite_adresse'][0], $adressePanier['tel_adresse'][0], $adressePanier['adresse_adresse'][0], $adressePanier['cp_adresse'][0], $adressePanier['ville_adresse'][0], $adressePanier['pays_adresse'][0]);
		if($adresseLivraison == 0){	
			$adresseLivraison = $uneAdresse->insertAdresse($adressePanier, 0); 
			$unMembreAvoirAdresse->addAvoir($idMembre, $adresseLivraison);
		}
		// Verifie si l'adresse de facturation existe deja
		$adresseFacturation = $uneAdresse->checkAdresse($adressePanier['nom_adresse'][1], $adressePanier['prenom_adresse'][1], $adressePanier['civilite_adresse'][1], $adressePanier['tel_adresse'][1], $adressePanier['adresse_adresse'][1], $adressePanier['cp_adresse'][1], $adressePanier['ville_adresse'][1], $adressePanier['pays_adresse'][1]);
		if($adresseFacturation == 0){
			$adresseFacturation = $uneAdresse->insertAdresse($adressePanier, 1);
			$unMembreAvoirAdresse->addAvoir($idMembre, $adresseFacturation);
		}
		// Crée une ligne commande
		$row = $this->createRow();
		// Prepare les colonnes de la ligne
		$row->date_cde = new Zend_Db_Expr('now()');
		$row->id_membre = $idMembre;
		$row->id_adr_livraison = $adresseLivraison;
		$row->id_adr_facturation = $adresseFacturation;
		$row->statut_cde = 1;	
		$row->statut_paiement = 0;	
		$row->type_paiement = $typePaiement;
		// Insert la ligne commande dans la bdd
		$numCommande = $row->save();
		
		// Retourne l'id de la commande
		return $numCommande['id_cde'];
	}
	
	/**
	 * Retourne les commandes en fonction du type
	 * @param integer $idMembre
	 * @return array
	 */
	public function getCommandes($idMembre){
		// Recupere les commandes du membre
		$result = $this	->select()
						->where('id_membre = ?', $idMembre);
		$commande = $this->fetchAll($result);
		
		return $commande->toArray();
	}
	
	/**
	 * Récupère les informations d'une commande
	 * @param integer $idMembre
	 * @param integer $idCommande
	 * @return array
	 */
	public function getLignesCommande($idMembre, $idCommande){
		// Prepare la requete
		$result = $this	->select()->setIntegrityCheck(false)
						->from(array('c' => $this->_name), array('*'))
						->joinLeft(array('m' => 'membre'), 'm.id = c.id_membre', array('*'))
						->joinLeft(array('lc' => 'ligne_cde'), 'lc.id_cde = c.id_cde', array('*'))
						->joinLeft(array('p' => 'produit'), 'lc.id_produit = p.id', array('*'))
						->joinLeft(array('t' => 'typeproduit'), 't.idType = p.idTypeProduit', array('*'))
						->joinLeft(array('sc' => 'souscategorie'), 'sc.idSousCategorie = p.idSousCategorieProduit', array('*'))
						->joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
						->joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
						->joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
						->where('c.id_membre = ?', $idMembre)
						->where('lc.id_cde = ?', $idCommande);
		// Execute la requete
		$ligneCommande = $this->fetchAll($result);
	
		return $ligneCommande->toArray();
	}
	
	/**
	 * Renvoi l'etat du paiement
	 * @param integer $idCommande
	 * @return bool
	 */
	public function etatPaiement($idCommande){
		$uneCommande = $this->select()
		->where('id_cde = ?', $idCommande)
		->where('statut_paiement = 1');
		// Si la commande est trouvée, cela veut dire que le paiement est ok (etat = 1)
		if($this->fetchRow($uneCommande)) return true;
		else return false;
	}
	
	/**
	 * Modifie le statut du paiement de la commmande
	 * @param integer $idCommande
	 */
	public function updateStatutPaiement($idCommande){
		$data = array('statut_paiement'=> 1);
		$this->update($data, 'id_cde='.(int)$idCommande);
	}
	
	
	/**
	 * Supprimer une commande
	 * @param integer $idCommande
	 */
	public function deleteCommande($idCommande){
		$uneCommande = $this->select()
					   		->where('id_cde = ?', $idCommande)
					   		->where('statut_paiement = 0');
		if($this->fetchRow($uneCommande)){
			// Supprime les lignes de la commande
			$ligneCommande = new Application_Model_DbTable_LigneCommande();
			$ligneCommande->deleteCommande($idCommande);
			// Supprime la commande
			$this->delete('id_cde='.(int)$idCommande);
		}
	}

}

