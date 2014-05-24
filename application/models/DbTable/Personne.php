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
						->where('idEntrepriseTravail = ?', $idEntreprise)
						->where('etatPersonne = 1');
		// Retourne le resultat de la requete
		return $this->fetchAll($result);
	}
	
	/**
	 * Fonction qui retourne les données d'une personne
	 * @param integer $idPersonne
	 * @param integer $idEntreprise
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getPersonne($idPersonne, $idEntreprise)
	{
		// Crée un objet select
		$result = $this	->select()
						->where('idPersonne = ?', $idPersonne)
						->where('idEntrepriseTravail = ?', $idEntreprise)
						->where('etatPersonne = 1');
		// Retourne le resultat de la requete
		return $this->fetchRow($result);
	}
	
	/**
	 * Fonction qui supprime une personne de la table
	 * Si exception (dépendance dans une autre table) on passe sont etat a -1
	 * @param integer $idPersonne
	 */
	public function deletePersonne($idPersonne){
		try {
			// Supprime l'entreprise
			$this->delete('idPersonne = '.$idPersonne);
		}catch (Exception $e){
			// Si une erreur est déclanché (dépendance de clé étrangere), on passe sont etat a -1
			$this->update(array('etatPersonne'=>-1), 'idPersonne = '.$idPersonne);
		}
	}
	
	/**
	 * Fonction qui insert une personne dans la bdd
	 * @param integer $codeEntreprise
	 * @param string $nomPersonne
	 * @param string $prenomPersonne
	 * @param string $fonctionPersonne
	 * @param string $telPortPersonne
	 * @param string $telPostePersonne
	 * @param string $emailPersonne
	 * @return integer
	 */
	public function insertPersonne($codeEntreprise, $nomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne){
		try {
			// Crée un ligne personne
			$row = $this->createRow();
			// Prepare les colonnes de la ligne
			$row->idEntrepriseTravail = $codeEntreprise;
			$row->nomPersonne = $nomPersonne;
			$row->prenomPersonne = $prenomPersonne;
			$row->fonctionPersonne = $fonctionPersonne;
			$row->telPortPersonne = $telPortPersonne;
			$row->telPostePersonne = $telPostePersonne;
			$row->emailPersonne = $emailPersonne;
			$row->etatPersonne = 1;
	
			// Insert la ligne dans la bdd et retourne son id
			return $row->save();
		} catch(Exeception $e) { return -1; }
	}

	/**
	 * Fonction qui modifie les données d'une personne
	 * @param integer $codeEntreprise
	 * @param string $nomPersonne
	 * @param string $prenomPersonne
	 * @param string $fonctionPersonne
	 * @param string $telPortPersonne
	 * @param string $telPostePersonne
	 * @param string $emailPersonne
	 * @param integer $codePersonne
	 * @return boolean
	 */
	public function updatePersonne($codeEntreprise, $nomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne, $codePersonne){
		try {
			// Param
			$data = array('idEntrepriseTravail'=>$codeEntreprise, 'nomPersonne'=>$nomPersonne,'prenomPersonne'=>$prenomPersonne,'fonctionPersonne'=>$fonctionPersonne,'telPortPersonne'=>$telPortPersonne, 'telPostePersonne'=>$telPostePersonne, 'emailPersonne'=>$emailPersonne);
			// Update
			$this->update($data, 'idPersonne = '. (int)$codePersonne);
			return true;
		} catch(Exeception $e) { return false; }
	}
		
}
