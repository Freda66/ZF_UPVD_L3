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
  	public function getStage($code, $session){
  		// Recupere les informations d'un stage
  		// Jointure sur personne pour recuperer le nom et prenom du tuteur de l'entreprise,...
  		$result = 	$this	->select()->setIntegrityCheck(false)
  							->from(array('s' => $this->_name), array('*'))
  							->joinLeft(array('p'=>'personne'), 's.idTuteur = p.idPersonne', array('*'))
  							->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idStage = s.codeStage', array('*'))
  							->joinLeft(array('e'=>'etudiant'), 'res.idEtudiant = e.ineEtudiant', array('*'))
  							->joinLeft(array('en'=>'enseignant'), 'res.idEnseignantTuteur = en.idEnseignant', array('*'))
  							->where('codeStage = ?', $code);
  		// Entreprise
  		if($session->type == "Entreprise") $result->where('idEntreprise = ?', $session->identifiant);
  		// Enseignant tuteur
  		if($session->type == "Enseignant" && !$session->isResponsable) $result->where('res.idEnseignantTuteur = ?', $session->identifiant);
  		
  		// Etudiant
  		if($session->type == "Etudiant") {
  			$result = $this->fetchAll($result);
  			
  			foreach($result as $unStage){
  				if($unStage->idEtudiant == $session->identifiant || $unStage->idEtudiant == null) return $unStage;
  			}
  			
  			// Pas de return avant donc return null
  			return null;  
  		} 
  		else {
  			$result = $this->fetchRow($result);
  		}
  		
  		// Retourne le resultat
  		return $result;
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
		return $this->fetchAll($result)->toArray();
	}
	
	/**
	 * Retourne la liste des stages qui sont disponible (etat = 1) ou en attente (etat = 0)
	 * @param int $etat : 1 valid default, 0 en attente (responsable)
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getStagesAllValidORAttente($etat = 1){
		$result = $this	->select()->setIntegrityCheck(false)
  						->from(array('s' => $this->_name), array('*'))
						->joinLeft(array('res'=>'realiseretudiantstage'), 'res.idStage = s.codeStage', array('*'))
						->where('etatStage = ?', $etat)
						->where('res.idStage is null');
		// Retourne le resultat de la requete						
		return $this->fetchAll($result);
	}
	
	/**
	 * Retourne la liste des stages (enseignant responsable)
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getStages(){
		// Retourne tout les stages
		return $this->fetchAll();
	}
}
