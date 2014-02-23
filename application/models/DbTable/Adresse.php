<?php

class Application_Model_DbTable_Adresse extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'adresse';

	// Recupere l'objet d'une adresse
	public function getAdresse($id)
	{
		// Recupere les informations de l'adresse
		$result = $this	->select()->setIntegrityCheck(false)
						->from(array('a' => $this->_name), array('*'))
						->joinLeft(array('p'=>'pays'), 'a.id_pays = p.id_pays', array('en'))
						->where('id_adr = ?', $id);
		$result = $this->fetchRow($result);
		
		return $result;
	}
	
	/**
	 * Verifie si l'adresse existe deja dans la base de données
	 * @param string $nom
	 * @param string $prenom
	 * @param string $civilite
	 * @param string $tel
	 * @param string $adresse
	 * @param string $cp
	 * @param string $ville
	 * @param integer $id_pays
	 */
	public function checkAdresse($nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $id_pays)
	{
		// Recupere le dernier id insérer
		$result = $this->select()
		->where('nom = ?', $nom)
		->where('prenom = ?', $prenom)
		->where('civilite = ?', $civilite)
		->where('tel = ?', $tel)
		->where('adresse = ?', $adresse)
		->where('codepostal = ?', $cp)
		->where('ville = ?', $ville)
		->where('id_pays = ?', $id_pays);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Retourne l'id de l'adresse si elle existe
		if($result) return $result->id_adr;
		else return 0;
	}
	
	public function getLastId($nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $id_pays)
	{
		// Recupere le dernier id insérer
		$result = $this->select()
				->where('nom = ?', $nom)
				->where('prenom = ?', $prenom)
				->where('civilite = ?', $civilite)
				->where('tel = ?', $tel)
				->where('adresse = ?', $adresse)
				->where('codepostal = ?', $cp)
				->where('ville = ?', $ville)
				->where('id_pays = ?', $id_pays)
				->order(array('id_adr DESC'))
				->limit(1);
		// Execute la requete 
		$result = $this->fetchAll($result);
		// Retourne l'id de la derniere adresse inseré
		return $result[0]->id_adr;
	}
	
	// Ajoute une adresse
	public function addAdresse($nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $id_pays)
	{
		// Récupere dans un tableau les valeurs de la ligne a inserer
		$data = array('nom'=>$nom,'prenom'=>$prenom,'civilite'=>$civilite,'tel'=>$tel,'adresse'=>$adresse,'codepostal'=>$cp,'ville'=>$ville,'id_pays'=>$id_pays);
		// Insert la ligne dans la bdd
		$this->insert($data);
		$idAdresseInsert = $this->getLastId($nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $id_pays);
		// Retourne l'id de l'adresse insérer
		return $idAdresseInsert;
	}
	
	public function insertAdresse($uneAdresse, $i)
	{
		// Crée un ligne adresse
		$row = $this->createRow();
		// Prepare les colonnes de la ligne
		$row->nom = $uneAdresse['nom_adresse'][$i];
		$row->prenom = $uneAdresse['prenom_adresse'][$i];
		$row->civilite = $uneAdresse['civilite_adresse'][$i];
		$row->tel = $uneAdresse['tel_adresse'][$i];
		$row->adresse = $uneAdresse['adresse_adresse'][$i];
		$row->codepostal = $uneAdresse['cp_adresse'][$i];
		$row->ville = $uneAdresse['ville_adresse'][$i];
		$row->id_pays = $uneAdresse['pays_adresse'][$i];
		// Insert la ligne dans la bdd et retourne son id
		$numAdresse = $row->save();
		
		return $numAdresse;
	}
	
	// Modifie une adresse en fonction de son id
	public function updateAdresse($idAdresse, $nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $id_pays)
	{
		// Récupere dans un tableau les nouvelles données a modifiée
		$data = array('nom'=>$nom,'prenom'=>$prenom,'civilite'=>$civilite,'tel'=>$tel,'adresse'=>$adresse,'codepostal'=>$cp,'ville'=>$ville,'id_pays'=>$id_pays);
		// Insert les nouvelles données, avec un WHERE="id", numéro de l'id récuperé
		$this->update($data, 'id_adr = '. (int)$idAdresse);
		// Retourne le resultat
		return true;
	}
	
	// Supprimer une adresse en fonction de son id
	public function deleteAdresse($idAdresse)
	{
		$this->delete('id_adr =' . (int)$idAdresse);
	}
	
	/* ************** */
	/* WEB SERVICE C# */
	/* ************** */
	public function getAdresseByIdWebServiceC($idAdresse){
		$result = $this->select()
		// Prepare la requete
		->where('id_adr = ?', $idAdresse);
		// Recupere le membre
		$adresse = $this->fetchRow($result);
	
		return (object)$adresse;
	}

}

