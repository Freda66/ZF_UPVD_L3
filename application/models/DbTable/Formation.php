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
    public $_nbItemByPage = 6;
    private $_nbPagePrint = 20;

    /**
     * Fonction qui retourne la liste entiere des formations
     */
    public function getFormations(){
    	return $this->fetchAll();
    }
    
    /**
     * Retourne sous pagination la liste des formations du site
     * @param integer $page
     * @return Zend_Paginator
     */
    public function getListeFormations($page){
    	// Crée un objet Pagination, en connectant la requete avec l'adaptateur de Zend Paginator
    	$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($this->select()));
    	// Détermine le nombre d'item par page
    	$paginator ->setItemCountPerPage($this->_nbItemByPage);
    	// Détermine la page en courrante
    	$paginator ->setCurrentPageNumber($page);
    	// Indique le nombre de numéro de page qu'on affiche
    	$paginator->setPageRange($this->_nbPagePrint);
    	// Retourne le resultat
    	return $paginator;
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
    
    /**
     * Insert une formation dans la bdd
     * @param integer $idEnseignant
     * @param string $libelle
     * @param integer $niveau
     * @param string $specialite
     * @return boolean
     */
    public function insertFormation($idEnseignant, $libelle, $niveau, $specialite){
    	try{
    		if($this->insert(array('idEnseignantResponsable'=>$idEnseignant, 'libelleFormation'=>$libelle, 'niveauFormation'=>$niveau, 'specialiteFormation'=>$specialite))) return true;
    		else return false;
    	} catch(Exception $e){
    		return false;
    	}
    }
    
    /**
     * Modifie une formation dans la bdd
     * @param integer $codeFormation
     * @param integer $idEnseignant
     * @param string $libelle
     * @param integer $niveau
     * @param string $specialite
     * @return boolean
     */
    public function updateFormation($codeFormation, $idEnseignant, $libelle, $niveau, $specialite){
    	try {
    		// Param
    		$data = array('idEnseignantResponsable'=>$idEnseignant, 'libelleFormation'=>$libelle, 'niveauFormation'=>$niveau, 'specialiteFormation'=>$specialite);
    		// Update
    		$this->update($data, 'codeFormation = '. (int)$codeFormation);
    		return true;
    	} catch(Exeception $e) { return false; }
    	
    }
    
    /**
     * Supprime une formation de la bdd
     * @param integer $idFormation
     * @return boolean
     */
    public function deleteFormation($idFormation){
    	try{
    		if($this->delete(array('codeFormation = '.(int)$idFormation))) return true;
    		else return false;
    	} catch (Exception $e) { return false; }
    }
    
}
