<?php

class Application_Model_DbTable_Membre extends Zend_Db_Table_Abstract
{
	// Nom de la table qu'on va gérer, on déclare une propriété protégée
    protected $_name = 'membre';

	// Recupere l'objet d'un membre
	public function getMembre($id)
	{
		// Place l'id récuperé dans une variable
		$id = (int)$id;
		// Cherche une ligne qui correspond a l'id récuperé dans la bdd
		$row = $this->fetchRow('id_membre = ' . $id);
		// Si on ne la trouve pas on transmet une Exception
		if (!$row) 
		{
			throw new Exception("Could not find row $id");
		}
		// Si on la trouve, on lui retourne toutes les valeurs correspondantes a cette ligne (id,nom,prenom,...)
		return $row->toArray();
	}
	
	// Fonction pour authentifié le client
	public function authentificationMembre($email, $mdp, $boolCryptMd5 = true)
	{
		// Recupere la valeur du code de validation du membre
		$membre = $this->select()
				->where('mail = ?', $email);
		$membre = $this->fetchRow($membre);
		if($membre->codeValid == 1)
		{
			// Crée un objet d'authentification a la bdd
			$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
	
			// Definit les valeurs de cette objet
			$authAdapter->setTableName('membre')
					  	->setIdentityColumn('mail')
					  	->setCredentialColumn('mdp');
						// Booléen qui indique si le mdp de connection a besoin d'etre crypter
						if($boolCryptMd5){
			$authAdapter->setCredentialTreatment("MD5(?)");
						}
			$authAdapter->setIdentity($email)
					  	->setCredential($mdp);
	
			// Crée un objet de session
			$auth = Zend_Auth::getInstance();
	
			// Authentification
	    	$result = $auth->authenticate($authAdapter);
	
			// Vérifie que le résultat est valide
			if ($result->isValid()) 
			{
				// Crée un espace de nom de session
				$authNamespace = new Zend_Session_Namespace('Zend_Auth');
	        	$result = true;
	    	}
			else 
			{
				$result = false;	
			}
		}
		else 
		{
			$result = false;	
		}
		
		return $result;
	}
	
	public function getMembreAuth()
	{
		// Recupere la session en cour (l'instance)
		$authentification = Zend_Auth::getInstance();
		// Recupre l'identifiant de la session en cour
		$identity = $authentification->getIdentity();
		
		// Recupere l'objet du membre connecter
		$membre = $this->select()
				->where('mail = ?', $identity);
		$membre = $this->fetchRow($membre);
		
		// Retourn l'objet membre récupere
		return $membre;		
	}
	
	public function getInformationMembre()
	{
		// Recupere la session en cour (l'instance)
		$authentification = Zend_Auth::getInstance();
		// Recupre l'identifiant de la session en cour
		$identity = $authentification->getIdentity();
		// Crée un objet select (setIntegrityCheck(false) nous permet d'appeler une autre table qui ne ce trouve pas dans notre méthode juste en lecture)
		$result = $this->select()->setIntegrityCheck(false)
			// Prepare la requete
			->from(array('m' => $this->_name), array('id_adr_habiter','pseudo','mail','datenaissance'))
			->joinLeft(array('a' => 'adresse'), 'm.id_adr_habiter = a.id_adr', array('nom','prenom','civilite','tel','adresse','codepostal','ville'))
			->joinLeft(array('p' => 'pays'), 'a.id_pays = p.id_pays', array('id_pays','en'))
			->where('mail = ?', $identity);
		// Execute la requete 
		$result = $this->fetchAll($result);
		// Retourne le resultat de la requete
		return $result;
	}
	
	public function getCommandes($type)
	{
		// Recupere l'objet membre authentifié
		$membre = $this->getMembreAuth();
		// Recupere les commandes du membre
		$result = $this->select()->setIntegrityCheck(false)
				->from(array('m' => $this->_name), array('id'))
				->joinLeft(array('c' => 'commande'), 'm.id = c.id_membre', array('*'))
				->where('c.id_membre = ?', $membre->id)
				->where('statut_cde = ?', $type);
		// Execute la requete 
		$result = $this->fetchAll($result);
		// Retourne la collection des commandes récupérées
		return $result;
	}

	public function getDetailsCommande($idCde)
	{
		// Recupere l'objet membre authentifié
		$membre = $this->getMembreAuth();
		
		// Prepare la requete
		$result = $this->select()->setIntegrityCheck(false)
			->from(array('m' => $this->_name), array('*'))
			->joinLeft(array('c' => 'commande'), 'm.id = c.id_membre', array('*'))
			->joinLeft(array('lc' => 'ligne_cde'), 'lc.id_cde = c.id_cde', array('*'))
			->joinLeft(array('p' => 'produit'), 'lc.id_produit = p.id', array('*'))
			->joinLeft(array('t' => 'typeproduit'), 't.idType = p.idTypeProduit', array('*'))
			->joinLeft(array('sc' => 'souscategorie'), 'sc.idSousCategorie = p.idSousCategorieProduit', array('*'))
			->joinLeft(array('l' => 'livre'), 'l.idProduit = p.id', array('*'))
			->joinLeft(array('a' => 'audio'), 'a.idProduit = p.id', array('*'))
			->joinLeft(array('v' => 'video'), 'v.idProduit = p.id', array('*'))
			->where('c.id_membre = ?', $membre->id)
			->where('lc.id_cde = ?', $idCde);
		// Execute la requete
		$result = $this->fetchAll($result);
		// Retourne le resultat
		return $result;
	}
	
	public function existProduitLigne($idProduit, $idCde)
	{
		// Recupere l'objet membre authentifié
		$membre = $this->getMembreAuth();
		
		// Prepare la requete
		$result = $this->select()->setIntegrityCheck(false)
			->from(array('m' => $this->_name), array('*'))
			->joinLeft(array('c' => 'commande'), 'm.id = c.id_membre', array('*'))
			->joinLeft(array('lc' => 'ligne_cde'), 'lc.id_cde = c.id_cde', array('*'))
			->where('c.id_membre = ?', $membre->id)
			->where('lc.id_produit = ?', $idProduit)
			->where('lc.type_vente = ?', 1)
			->where('lc.id_cde = ?', $idCde);
		// Execute la requete
		$result = $this->fetchRow($result);
		// Retourne le resultat
		return $result;
	}
	
	// Fonction qui retourne true ou false pour savoir si le membre peut visionner un film
	public function canWatchMovie($idProduit, $idCde)
	{
		// Recupere l'objet commande de l'id passer en parametre
		$commande = $this->existProduitLigne($idProduit, $idCde);
		
		// Explose la date de commande
		$date = explode('-', $commande->date_cde); // Date mois, et annees
		$dateJ = explode(' ',$date[2]); // Date jours
		$dateFin = $dateJ[0] + 3; // Détermine la fin de la location (72h apres)
		if ($dateFin < 10) { $dateFin = '0'.$dateFin; }
		$date_cde = $date[0].'-'.$date[1].'-'.$dateFin.' '.$dateJ[1];
		
		// Explose la date actuel
		$dateNow = explode(' ', date("20y-m-d H:i:s")); // Separe les jours mois et années avec les heures minutes et secondes
		$dateNowTime = explode(':', $dateNow[1]); // Sépare les heures minutes secondes
		$dateNowTime[0] = $dateNowTime[0]+1; // Ajoute le fuseau horaire de Paris
		$dateNow = $dateNow[0].' '.$dateNowTime[0].':'.$dateNowTime[1].':'.$dateNowTime[2]; // Concatene la date actuel
		
		// Determine si la video peut etre regardée
		if($dateNow < $date_cde){ $result = true; } else { $result = false; }
		
		return $result;
	}
	
	public function getLastId($idAdresse,$pseudo,$mdp,$mail,$dateNaissance)
	{
		// Recupere le dernier id insérer
		$result = $this->select()
				->where('id_adr_habiter = ?', $idAdresse)
				->where('pseudo = ?', $pseudo)
				->where('mdp = ?', MD5($mdp))
				->where('mail = ?', $mail)
				->where('datenaissance = ?', $dateNaissance)
				->order(array('id DESC'))
				->limit(1);
		// Execute la requete 
		$result = $this->fetchAll($result);
		// Retourne l'id du dernier membre inseré
		return $result[0]->id;
	}
	
	public function recupMdpMembre($mail)
	{
		/* RECUPERE LE MEMBRE */
		$result = $this->select()
				->where('mail = ?', $mail);
		$result = $this->fetchRow($result);
		if($result){ $boolResult = true; } else { $mdp = false; }
		
		if($boolResult == true){
			/* MODIFIE LE MOT DE PASSE */
			$mdp = rand();
			$data = array('mdp'=>MD5($mdp));
			$this->update($data, 'id = '.$result->id);
		}
		
		// Retourne le nouveau mot de passe
		return $mdp;
	}
	
	// Ajoute un membre
	public function addMembre($pseudo, $mdp, $mail, $dateNaissance, $nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $pays)
	{
		/* VERIFIE SI LE MEMBRE EXISTE */
		$result = $this->select()
				->where('mail = ?', $mail);
		$result = $this->fetchRow($result);
		
		if($result)
		{
			return false;
		} 
		else 
		{
			/* PROCEDURE STOCKEE (bug idMembre return) */
			// Crypte le mot de passe et le pseudo
			//$mdp = MD5($mdp); $codePseudo = MD5($pseudo);
			// Appel la procédure stockée qui s'occupe d'inserer le membre
			//$this->_db->exec('CALL insertMembre("'.$pseudo.'", "'.$mdp.'", "'.$mail.'", "'.$dateNaissance.'", "0", "'.$codePseudo.'", "'.$nom.'", "'.$prenom.'", "'.$civilite.'", "'.$tel.'", "'.$adresse.'", "'.$cp.'", "'.$ville.'", "'.$pays.'")');
			
			/* INSERT L'ADRESSE */
			// Crée un objet dbTable adresse
			$adresseObject = new Application_Model_DbTable_Adresse();
			// Modifie l'adresse du membre
			$idAdresseInsert = $adresseObject->addAdresse($nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $pays);
			
			/* INSERT LE MEMBRE */
			// Récupere dans un tableau les valeurs de la ligne a inserer
			$data = array('id_adr_habiter'=>$idAdresseInsert,'pseudo'=>$pseudo,'mdp'=>MD5($mdp),'mail'=>$mail,'datenaissance'=>$dateNaissance,'codeValid'=>0,'codepseudo'=>MD5($pseudo));
			//// Insert la ligne dans la bdd
			$this->insert($data);
			$idMembreInsert = $this->getLastId($idAdresseInsert,$pseudo,$mdp,$mail,$dateNaissance);
			
			/* INSERT DANS LA TABLE AVOIR */
			// Crée un objet dbTable avoir (adresse/membre)
			$avoirAdresse = new Application_Model_DbTable_AvoirAdresse();
			// Insert l'adresse du membre
			$avoirAdresse->addAvoir($idMembreInsert, $idAdresseInsert);
			
			return $idMembreInsert;
		}
	}

	public function updateCodeValidation($idMembre, $codePseudo)
	{
		/* VERIFIE SI LE MEMBRE EXISTE */
		$result = $this->select()
				->where('id = ?', $idMembre)
				->where('codepseudo = ?', $codePseudo);
		$result = $this->fetchRow($result);
		
		if($result)
		{
			// Crée la requete
			$data = array('codeValid'=>1);
			// Insert la ligne dans la bdd
			$this->update($data, 'id ='.(int)$idMembre);	
		} 
		else 
		{
			$result = false;
		}
		
		return $result;
	}
	
	// Modifie les informations d'un membre
	public function updateMembre($idAdresse, $nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $pays)
	{
		// Crée un objet dbTable adresse
		$adresseObject = new Application_Model_DbTable_Adresse();
		// Modifie l'adresse du membre
		$result = $adresseObject->updateAdresse($idAdresse, $nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $pays);
		// Retourne le resultat
		return $result;
	}
	
	public function verifMdp($idMembre, $mdpActuel)
	{
		// Verifie si on trouve l'objet membre, on vérifie le mdp passé en parametre avec l'id du membre
		$membre = $this->select()
				->where('mdp = ?', MD5($mdpActuel))
				->where('id = ?', $idMembre);
		$membre = $this->fetchRow($membre);
		// Si on recupere un objet on valide si non false
		if($membre){ $result = true; } else { $result = false; }
		// Retourne true ou false
		return $result;	
	}
	
	public function updateMdpMembre($idMembre, $mdp)
	{
		// Récupere dans un tableau les valeurs de la ligne a inserer
		$data = array('mdp'=>MD5($mdp));
		// Insert la ligne dans la bdd
		$this->update($data, 'id ='.(int)$idMembre);
		// Retourne que la modification a été effectuer
		return true;
	}
	
	public function desactiverMembre($idMembre)
	{
		// Récupere dans un tableau les valeurs de la ligne a inserer
		$data = array('codeValid'=> 0);
		// Insert la ligne dans la bdd
		$this->update($data, 'id ='.(int)$idMembre);
		// Retourne que la modification a été effectuer
		return true;
	}
	
	// Supprimer un membre en fonction de son id
	public function supprimerMembre($idMembre)
	{
		// Check que le membre n'a pas de commande
		$result = $this->select()->setIntegrityCheck(false)
			->from(array('m' => $this->_name), array('*'))
			->joinLeft(array('c' => 'commande'), 'm.id = c.id_membre', array('*'))
			->where('c.id_membre = ?', $idMembre);
		$result = $this->fetchRow($result);
		// Si il a déjà passée une commande, on désactive son compte
		if($result)
		{
			$data = array('codeValid'=> 0);
			$this->update($data, 'id ='.(int)$idMembre);
		}
		// Si non on le supprime
		else 
		{
			$this->delete('id =' . (int)$idMembre);
		}
		
		return true;
	}
	
	
	
	/* *********** */
	/* WEB SERVICE */
	/* *********** */
	/**
	 * Retourne l'objet du membre
	 * @param string $email
	 * @param string $mdp
	 * @return array
	 */
	public function getMembreByLogWebService($email, $mdp){
		// Recupere la valeur du code de validation du membre
		$result = $this->select()->setIntegrityCheck(false)
			// Prepare la requete
			->from(array('m' => $this->_name))
			->joinLeft(array('a' => 'adresse'), 'm.id_adr_habiter = a.id_adr', array('nom','prenom','civilite','tel','adresse','codepostal','ville'))
			->joinLeft(array('p' => 'pays'), 'a.id_pays = p.id_pays', array('id_pays','en'))
		   	->where('mail = ?', $email)
		   	->where('mdp = ?', MD5($mdp))
		   	->where('codeValid = ?', 1);
		// Recupere le membre
		$membre = $this->fetchRow($result);

		return $membre->toArray();
	}
	
	/**
	 * Valid le code de validation du membre avec son id
	 * @param integer $idMembre
	 */
	public function updateCodeValidationAuto($idMembre)
	{
		// Crée la requete
		$data = array('codeValid'=>1);
		// Insert la ligne dans la bdd
		$this->update($data, 'id ='.(int)$idMembre);
	}
	
	
	/* ************** */
	/* WEB SERVICE C# */
	/* ************** */
	public function getMembreByLogWebServiceC($email, $mdp){
		// Recupere la valeur du code de validation du membre
		$result = $this->select()->setIntegrityCheck(false)
		// Prepare la requete
		->from(array('m' => $this->_name))
		->where('mail = ?', $email)
		->where('mdp = ?', MD5($mdp))
		->where('codeValid = ?', 1);
		// Recupere le membre
		$membre = $this->fetchRow($result);
	
		return (object)$membre;
	}
	
}
