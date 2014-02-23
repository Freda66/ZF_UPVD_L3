<?php

// Inclu la classe Produit
require_once APPLICATION_PATH . '/models/Class/Produit.php';

class Application_Model_DbTable_Produit extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'produit';
	
	// Recupere un objet produit
	public function getProduitById($idProduit)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
				-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
				-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
				-> where('p.id = ?', $idProduit);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Si on la trouve, on lui retourne toutes les valeurs correspondantes a cette ligne (id,titre,prix,...)
		return $result;
	}
	
	// Recupere tout les objets produits
	public function getProduitsAll()
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
		-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
		-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
		-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
		-> joinLeft(array('vl' => 'video_location'), 'vl.idProduit = p.id', array('*'));
		// Execute la requete
		$result = $this->fetchAll($result);

		return $result;
	}
	
	// Recupere tout les objets produits en pagination
	public function getProduitsAllPagination($page)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
		-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
		-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
		-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
		-> joinLeft(array('vl' => 'video_location'), 'vl.idProduit = p.id', array('*'));
			
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
		// Détermine le nombre d'item par page
	    $paginator ->setItemCountPerPage(5);
		// Détermine la page en courrante
	    $paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange(15);
		// Retourne le resultat
	    return $paginator;
	}
	
	// Recupere tout les objets produits
	public function getProduitsTopAll($nature)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
		-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
		-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
		-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
		->order(array("$nature DESC", 'p.id'));
		// Execute la requete
		$result = $this->fetchAll($result);
	
		return $result;
	}
	
	// Retourne la quantité stock d'un produit
	public function getQteProduit($idProduit){
		$result = $this	->select()
		->where('id = ?', $idProduit);
		$result = $this->fetchRow($result);
	
		return $result->qteStock;
	}
	
	public function getProduitLocationById($id)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('vl' => 'video_location'), 'vl.idProduit = p.id', array('*'))
				-> where('vl.idProduit = ?', $id);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Si on la trouve, on lui retourne toutes les valeurs correspondantes a cette ligne (id,titre,prix,...)
		return $result;
	}
	
	// Recupere un resultat a partir d'un choix
	public function getChoixRequetteProduit($choix, $requette, $page=1)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
				-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
				-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
				-> where($choix.' = ?', $requette);
		
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
		// Détermine le nombre d'item par page
	    if($choix == 'idTypeproduit'){ $paginator ->setItemCountPerPage(5); }
		else { $paginator ->setItemCountPerPage(2); }
		// Détermine la page en courrante
	    $paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange(15);
		// Retourne le resultat
	    return $paginator;
	}
	
	// Recupere un resultat a partir d'un choix
	public function getChoixRequetteProduitNouveautes($choix, $requette, $limit)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false)
				 				 -> from(array('p' => $this->_name), array('*'))
			 					 -> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
			 					 -> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
			 					 -> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'));
		if($choix!=null) $result -> where($choix.' = ?', $requette);
		$result 				 ->order(array('dateAjout DESC', 'p.id DESC'))
								 ->limit($limit);
		// Execute la requete
		$result = $this->fetchAll($result);
		// Retourne le resultat
		return $result;
	}
	
	// Recupere les produits en promotions a partir de leur type
	public function getPromotionByTypeProduit($type = 0)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
				-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
				-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
				-> where('prixPromo != null')
				-> orwhere('prixPromo != 0')
				-> where('qteStock > 0');
		$result ->order(array('prixPromo DESC', 'p.id'))
				->limit(9);
		// Si on envoi un type on recupere en fonction du type ceux en promo
		if($type != 0){ $result -> where('idTypeProduit = ?', $type); }
		
		$result = $this->fetchAll($result);			
		return $result;
	}
	
	// Recupere un resultat a partir des produits en location
	public function getLocationProduit($page=1)
	{
		// Crée un objet select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
				-> joinLeft(array('vl' => 'video_location'), 'vl.idProduit = p.id', array('*'))
				-> where('p.id IN (vl.idProduit)');
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
		// Détermine le nombre d'item par page
	    $paginator ->setItemCountPerPage(2);
		// Détermine la page en courrante
	    $paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange(15);
		// Retourne le resultat
	    return $paginator;
	}
	
	/**
	 * Recupere un ou plusieurs produit en fonction de la recherche
	 * @param $search == Parametre de la rechercher
	 * @param $page == Numéro de la page
	 * @return Zend_Paginator
	 */
	public function getProduitSearch($search, $page)
	{
		// Creé un object select
		$result = $this->select()->setIntegrityCheck(false);
		// Prepare la requete
		$result -> from(array('p' => $this->_name), array('*'))
				-> joinLeft(array('t' => 'typeproduit'), 't.idType = p.idTypeProduit', array('*'))
				-> joinLeft(array('s' => 'souscategorie'), 's.idSousCategorie = p.idSousCategorieProduit', array('*'))
				-> joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
				-> joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
				-> joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
				-> where('libelle LIKE ? "%"',$search,'"%"')
				-> orWhere('prix LIKE ? "%"',$search,'"%"')
				-> orWhere('libelleType LIKE ? "%"',$search,'"%"')
				-> orWhere('libelleSousCategorie LIKE ? "%"',$search,'"%"')
				-> orWhere('EAN13 LIKE ? "%"',$search,'"%"')
				-> orWhere('sous_titre LIKE ? "%"',$search,'"%"')
				-> orWhere('auteur LIKE ? "%"',$search,'"%"')
				-> orWhere('editeur LIKE ? "%"',$search,'"%"')
				-> orWhere('annee LIKE ? "%"',$search,'"%"')
				-> orWhere('interprete LIKE ? "%"',$search,'"%"')
				-> orWhere('realisateur LIKE ? "%"',$search,'"%"')
				-> orWhere('acteur LIKE ? "%"',$search,'"%"')
				-> orWhere('sous_titrage LIKE ? "%"',$search,'"%"');		
		// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($result));
		// Détermine le nombre d'item par page
	    $paginator ->setItemCountPerPage(5);
		// Détermine la page en courrante
	    $paginator ->setCurrentPageNumber($page);
		// Indique le nombre de numéro de page qu'on affiche
		$paginator->setPageRange(15);
		// Retourne le resultat
	    return $paginator;
	}
	
	public function getProduitTopVenteOrClassement($nature, $type, $limit)
	{
		// Crée un objet select (setIntegrityCheck(false) nous permet d'appeler une autre table qui ne ce trouve pas dans notre méthode juste en lecture)
		$result = $this->select()->setIntegrityCheck(false)
			// Prepare la requete
			->from(array('p' => $this->_name), array('*', 'nbTop' => 'SUM('.$nature.')'))
			->joinLeft(array('t' => 'typeproduit'), 't.idType = p.idTypeProduit', array('*'))
			->joinLeft(array('c' => 'souscategorie'), 'c.idSousCategorie = p.idSousCategorieProduit', array('*'))
			->joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
			->joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
			->joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'));
			if($type != null){ $result->where('idTypeProduit = ?', $type); }
		$result->group('p.id')
			->order(array('nbTop DESC', 'p.id'))
			->limit($limit);
		// Execute la requete 
		$result = $this->fetchAll($result);
		// Retourne le resultat de la requete (5 meilleur produit de leur type)
		return $result;
	}
	
	public function setTopClassement($idProduit, $etat)
	{
		// Recupere le nb de topClassement
		$produit = $this->getProduitById($idProduit);
		$topClassement = $produit->topClassement;
		// Incrémente ou décrémente ou aucune action si l'etat a était modifié dans l'url
		if($etat == 1){ $topClassement++; } else if($etat == 0) { $topClassement--; } 
		// Prepare la requete
		$data = array('topClassement'=>$topClassement);
		// Modification
		$this->update($data, 'id ='.(int)$idProduit);

		return true;
	}

	/**
	 * Supprime ou Ajoute la quantite d'un produit
	 * $addTop = true par defaut (pour ajouter le top)
	 * $addTop = false (pour enlever le top)
	 * @param integer $idProduit
	 * @param integer $qteCommande
	 * @param bool $addTop
	 * @return boolean
	 */
	public function setTopVente($idProduit, $qteCommande, $addTop=true)
	{
		// Recupere le nb de vente du produit
		$produit = $this->getProduitById($idProduit);
		$topVente = $produit->topVente;
		// Incrémente sa valeur en fonction de la quantité commandée
		if($addTop) $topVente += $qteCommande; else $topVente -= $qteCommande;
		// Prepare la requete
		$data = array('topVente'=>$topVente);
		// Modification
		$this->update($data, 'id ='.(int)$idProduit);

		return true;
	}
	
	/**
	 * Supprime ou Ajoute la quantite d'un produit
	 * $delete = true par defaut (pour enlever la quantite)
	 * $delete = false (pour ajouter la quantite)
	 * @param integer $idProduit
	 * @param integer $qteCommande
	 * @param bool $delete
	 * @return boolean
	 */
	public function deleteQteProduit($idProduit, $qteCommande, $delete=true)
	{
		// Recupere la quantité du produit
		$produit = $this->getProduitById($idProduit);
		$qteProduit = $produit->qteStock;
		// Diminue sa valeur en fonction de la quantité commandée
		if($delete) $qteProduit -= $qteCommande; else $qteProduit += $qteCommande;
		// Prepare la requete
		$data = array('qteStock'=>$qteProduit);
		// Modification
		$this->update($data, 'id ='.(int)$idProduit);
	
		return true;
	}
	
	/* ************** */
	/* WEB SERVICE C# */ 
	/* ************** */
	public function getCollectionProduit(){
		// Recupere la liste des produits
		$produits = $this->fetchAll();
		
		// Crée un tableau Produits
		$lesProduits = array();
		
		// Affecte les produits dans un tableau
		foreach($produits as $p){
			// Crée un objet Produit
			$unProduit = new Produit();
			// Affecte les valeurs du produit dans l'objet
			$unProduit->id = $p['id'];
			$unProduit->idTypeProduit = $p['idTypeProduit'];
			$unProduit->idSousCategorieProduit = $p['idSousCategorieProduit'];
			$unProduit->topVente = $p['topVente'];
			$unProduit->topClassement = $p['topClassement'];
			$unProduit->libelle = $p['libelle'];
			$unProduit->prix = $p['prix'];
			$unProduit->prixPromo = $p['prixPromo'];
			$unProduit->qteStock = $p['qteStock'];
			$unProduit->description = $p['description'];
				
			// Insere l'objet dans le tableau
			$lesProduits[] = $unProduit;
		}
		
		return $lesProduits;
	}
	
}

