<?php

/**
 * Class d'access a la table Stage
 * @author Fred
 *
 */
class Application_Model_DbTable_Stage extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'stage';

    /**
     * Fonction qui retourne l'enregistrement d'un stage
     * @param integer $code
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
  	public function getStage($code){
  		// Recupere les informations d'un stage
  		// Jointure sur personne pour recuperer le nom et prenom du tuteur de l'entreprise
  		$result = $this	->select()->setIntegrityCheck(false)
  						->from(array('s' => $this->_name), array('*'))
  						->joinLeft(array('p'=>'personne'), 's.idTuteur = p.idPersonne', array('*'))
  						->where('codeStage = ?', $code);
  		return $this->fetchRow($result);
  	}
    
	/**
	 * Fonction qui retourne les stages d'une entreprise à partir d'un numéro de siret
	 * @param string $siret
	 */
	public function getStagesEntreprise($siret){
		// Requete qui recupere les stages d'un entreprise
		$result = $this	->select()
						->where('idEntreprise = ?', $siret);
		// Retourne le resultat de la requete						
		return $this->fetchAll($result);
	}
	
	/**
	 * Retourne la liste des stages qui sont disponible (etat = 1) ou en attente (etat = 0)
	 * @param int $etat : 1 valid default, 0 en attente (responsable)
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getStagesAllValidORAttente($etat = 1){
		$result = $this	->select()
						->where('etatStage = ?', $etat);
		// Retourne le resultat de la requete						
		return $this->fetchAll($result);
	}
}
