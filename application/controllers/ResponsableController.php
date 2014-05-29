<?php

class ResponsableController extends Zend_Controller_Action
{

	/**
	 * Init au chargement du controller
	 */
    public function init()
    {
    	// Verifie que c'est un enseignant responsable
    	$session = Zend_Auth::getInstance()->getStorage()->read();
		if(!(isset($session->infoUser->isResponsable) && $session->infoUser->isResponsable)){
			// Message flash + Redirection
			$this->_helper->flashMessenger->addMessage(array('danger'=>'Pour accéder à l\'administration, veuillez vous connecter en tant qu\'enseignant responsable.'));
			$this->redirect('/index/index/');
		}
    }

    /**
     * Affiche la liste des enseignants, etudiant, et entreprise du site
     * Bouton modifier disponible
     */
    public function indexAction()
    {
    	$this->view->title = "Liste"; // Titre de la page
    	
    	// Objet model dbTable (enseignant, etudiant, entreprise)
    	$modelEnseignant = new Application_Model_DbTable_Enseignant();
    	$modelEtudiant = new Application_Model_DbTable_Etudiant();
    	$modelEntreprise = new Application_Model_DbTable_Entreprise();
    	
		// Recupere le numero de page d'un enseignant, etudiant, entreprise
    	$pageEnseignant = $this->getRequest()->getParam('pageEnseignant');
    	$pageEtudiant = $this->getRequest()->getParam('pageEtudiant');
    	$pageEntreprise = $this->getRequest()->getParam('pageEntreprise');
    	if(empty($pageEnseignant))	{ $pageEnseignant = 1; }
    	if(empty($pageEtudiant))	{ $pageEtudiant = 1; }
    	if(empty($pageEntreprise))	{ $pageEntreprise = 1; }
    	
    	// Recupere la liste des utilisateurs
    	$lesEnseignants = $modelEnseignant->getListeEnseignant($pageEnseignant);
    	$lesEtudiants = $modelEtudiant->getListeEtudiant($pageEtudiant);
    	$lesEntreprises = $modelEntreprise->getListeEntreprise($pageEntreprise);
    	
    	$this->view->lesEnseignants = $lesEnseignants;
    	$this->view->lesEtudiants = $lesEtudiants;
    	$this->view->lesEntreprises = $lesEntreprises;
    }
    
    /**
     * Fiche détaillée d'un utilisateur + liste des stages
     */
    public function ficheAction()
    {
    	$this->view->title = "Utilisateur"; // Titre de la page
    	 
    	// Recupere l'id de l'utilisateur et son type
    	$codeUtilisateur = $this->getRequest()->getParam('code');
    	$typeUtilisateur = $this->getRequest()->getParam('type');
    	 
    	if($codeUtilisateur != null){
    		// Crée un objet dbTable RealiserEtudiantStage
    		$modelStage = new Application_Model_DbTable_RealiserEtudiantStage();
    		
    		if($typeUtilisateur == "Enseignant") {
	    		// Crée un objet dbtable enseignant
	    		$modelEnseignant = new Application_Model_DbTable_Enseignant();
	    		// Recupere les informations d'un enseignant
	    		$unUtilisateur = $modelEnseignant->getEnseignant($codeUtilisateur);
	    		// Recupere les stages dont il est tuteur
	    		$lesStages = $modelStage->getStagesTuteur($codeUtilisateur, null, null);
    		} else if($typeUtilisateur == "Etudiant") {
	    		// Crée un objet dbtable etudiant
	    		$modelEtudiant = new Application_Model_DbTable_Etudiant();
	    		// Recupere les informations d'un etudiant
	    		$unUtilisateur = $modelEtudiant->getEtudiant($codeUtilisateur);
	    		// Recupere les stages qu'il a ou va réaliser
	    		$lesStages = $modelStage->getMyStages($codeUtilisateur, null, null, null);
    		} else if($typeUtilisateur == "Entreprise") {
	    		// Crée un objet dbTable Stage
	    		$modelStage = new Application_Model_DbTable_Stage();
	    		// Crée un objet dbTable Personne
	    		$modelPersonne = new Application_Model_DbTable_Personne();
	    		// Crée un objet dbtable entreprise
	    		$modelEntreprise = new Application_Model_DbTable_Entreprise();
	    		// Recupere les informations d'une entreprise
	    		$unUtilisateur = $modelEntreprise->getEntreprise($codeUtilisateur);
	    		// Recupere les employes de l'entreprise
	    		$lesEmployes = $modelPersonne->getPersonneByEntreprise($codeUtilisateur);
	    		// Recupere les stages déposés
	    		$lesStages = $modelStage->getStagesEntrepriseANDRealiser($codeUtilisateur);
	    		// Envoi a la vue les employes de l'entreprise
	    		$this->view->lesEmployes = $lesEmployes;
    		} 
    		
    		if($unUtilisateur == null){
    			// Message flash + Redirection
    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun utilisateur ne correspond.'));
    			$this->redirect('/responsable/index/');
    		} else {
	    		// Envoi a la vue les informations
	    		$this->view->unUtilisateur = $unUtilisateur;
	    		$this->view->typeUtilisateur = $typeUtilisateur;
	    		$this->view->lesStages = $lesStages;
    		}
    	} else {
    		// Message flash + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune fiche ne correspond.'));
    		$this->redirect('/responsable/index/');
    	}
    }
    
    /**
     * Supprime un utilisateur (passe son etat a -1)
     */
    public function deleteAction(){
    	// Recupere son type et son id
    	$codeUtilisateur = $this->getRequest()->getParam('code');
    	$typeUtilisateur = $this->getRequest()->getParam('type');
    	$isOk = false;
    	
    	if($typeUtilisateur == "Enseignant"){
    		// Crée un objet dbTable Enseignant
    		$modelEnseignant = new Application_Model_DbTable_Enseignant();
    		// Supprime l'enregistrement, si error (dependance dans d'autre table) => passe son etat a -1
    		$modelEnseignant->deleteEnseignant($codeUtilisateur);
    		$isOk = true;
    	} else if($typeUtilisateur == "Etudiant"){
    		// Crée un objet dbTable Etudiant
    		$modelEtudiant = new Application_Model_DbTable_Etudiant();
    		// Supprime l'enregistrement, si error (dependance dans d'autre table) => passe son etat a -1
    		$modelEtudiant->deleteEtudiant($codeUtilisateur);
    		$isOk = true;
    	} else if($typeUtilisateur == "Entreprise"){
    		// Crée un objet dbTable Entreprise
    		$modelEntreprise = new Application_Model_DbTable_Entreprise();
    		// Supprime l'enregistrement, si error (dependance dans d'autre table) => passe son etat a -1
    		$modelEntreprise->deleteEntreprise($codeUtilisateur);
    		$isOk = true;
    	} else if($typeUtilisateur == "Personne"){
    		// Crée un objet dbTable Personne
    		$modelPersonne = new Application_Model_DbTable_Personne();
    		// Supprime l'enregistrement, si error (dependance dans d'autre table) => passe son etat a -1
    		$modelPersonne->deletePersonne($codeUtilisateur);
    		$isOk = true;
    	} 
    	
    	// Message + Redirection 
    	if($isOk) $this->_helper->flashMessenger->addMessage(array('success'=>'L\'enregistrement a été supprimé.'));
    	else $this->_helper->flashMessenger->addMessage(array('danger'=>'Impossible de supprimer l\'enregistrement correspondant.'));
    	$this->redirect('/responsable/index/');
    }
    
    /**
     * Formulaire de dépot d'un enseignant
     * Ajouter/Modifier
     */
    public function depotenseignantAction()
    {
    	$this->view->title = "Enregistrement d'un enseignant"; // Titre de la page
    	
    	// Recupere le code de l'enseignant passé en param (si exist)
    	$codeEnseignant = $this->getRequest()->getParam('code');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Enseignant
    	$modelEnseignant = new Application_Model_DbTable_Enseignant();
    	// Crée un objet dbTable model enseigner formation
    	$modelEnseignerFormation = new Application_Model_DbTable_EnseignerFormationEnseignant();
    	
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		if($codeEnseignant == null){
    			// Formulaire de depot d'un enseignant
    			$formEnseignant = new Application_Form_DepotEnseignant();
    			$formEnseignant->setTranslator(Bootstrap::_initTranslate());
    			// Titre du formulaire
    			$this->view->titreForm = "Nouvel enseignant";
    		} else {
    			// Recupere les informations d'un enseignant
    			$unEnseignant = $modelEnseignant->getEnseignant($codeEnseignant);
    
    			if($unEnseignant == null){
    				$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun enseignant ne correspond.'));
    				$this->redirect('/responsable/index/');
    			} else {
    				// Recupere la liste des formations enseignées
    				$lesFormationsEnseigner = $modelEnseignerFormation->getEnseignerFormation($codeEnseignant);
    				$options = $lesFormationsEnseigner;
    				// Envoi le detail d'un enseignant au formulaire
    				$formEnseignant = new Application_Form_DepotEnseignant($options);
    				$formEnseignant->setTranslator(Bootstrap::_initTranslate());
    				$formEnseignant->populate($unEnseignant->toArray());
    			}
				// Titre du formulaire
    			$this->view->titreForm = "Modification enseignant";
    		}
    
    		// Traitement du formulaire
    		// Si le formulaire a été posté
    		if($this->getRequest()->isPost()) {
    			// Recupere les informations du formulaire
    			$formData = $this->getRequest()->getPost();
    
    			// Si les informations sont valides par rapport au formulaire init (initiale)
    			if($formEnseignant->isValid($formData)) {
    				// Recupere les attributs dans des variables
    				$nomEnseignant = $formEnseignant->getValue('nomEnseignant');
    				$prenomEnseignant = $formEnseignant->getValue('prenomEnseignant');
    				$fonctionEnseignant = $formEnseignant->getValue('fonctionEnseignant');
    				$specialiteEnseignant = $formEnseignant->getValue('specialiteEnseignant');
    				$loginEnseignant = $formEnseignant->getValue('loginEnseignant');
    				$mdpEnseignant = $formEnseignant->getValue('mdpEnseignant');
    				$isResponsableSiteEnseignant = $formEnseignant->getValue('isResponsableSiteEnseignant');
    				$lesFormations = $formEnseignant->getValue('idFormation');
    				
    				// Insert
    				if($codeEnseignant == null) {
    					$idEnseignant = $modelEnseignant->insertEnseignant($nomEnseignant, $prenomEnseignant, $fonctionEnseignant, $specialiteEnseignant, $loginEnseignant, $mdpEnseignant, $isResponsableSiteEnseignant);
    					if($idEnseignant != -1){
    						// Vide la table
    						$modelEnseignerFormation->deleteEnseignantFormation($idEnseignant);
    						// Insert les formations pour l'enseignant
    						foreach($lesFormations as $idFormation) {
    							$modelEnseignerFormation->insertEnseignantFormation($idFormation, $idEnseignant);
    						}
    						
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'enseignant a été enregistré avec succès.'));
    						$this->redirect("/responsable/fiche/code/$idEnseignant/type/Enseignant/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de l\'enseignant.'));
    						$formEnseignant->populate($formData);
    					}
    				}
    				// Update
    				else {
    					if($modelEnseignant->updateEnseignant($nomEnseignant, $prenomEnseignant, $fonctionEnseignant, $specialiteEnseignant, $loginEnseignant, $mdpEnseignant, $isResponsableSiteEnseignant, $codeEnseignant)) {
    						// Vide la table
    						$modelEnseignerFormation->deleteEnseignantFormation($codeEnseignant);
    						// Insert les formations pour l'enseignant
    						foreach($lesFormations as $idFormation) {
    							$modelEnseignerFormation->insertEnseignantFormation($idFormation, $codeEnseignant);
    						}
    						
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'enseignant a été modifié avec succès.'));
    						$this->redirect("/responsable/index/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification de l\'enseignant.'));
    						$formEnseignant->populate($formData);
    					}
    				}
    			} else $formEnseignant->populate($formData);
    		}
    
    		// Envoie a la vue le formulaire d'enseignant
    		$this->view->formEnseignant = $formEnseignant;
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('info'=>'Aucune page de ce nom n\'a été trouvé.'));
    		$this->redirect("/index/index/");
    	}
    }
    
    /**
     * Formulaire de dépot d'une entreprise
     * Ajouter/Modifier
     */
    public function depotentrepriseAction()
    {
    	$this->view->title = "Enregistrement d'une entreprise"; // Titre de la page
    	 
    	// Recupere le code de l'entreprise passé en param (si exist)
    	$codeEntreprise = $this->getRequest()->getParam('code');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Entreprise
    	$modelEntreprise = new Application_Model_DbTable_Entreprise();
    	// Crée un objet dbTable model Personne (qui travail dans l'entreprise)
    	$modelPersonne = new Application_Model_DbTable_Personne();
    	 
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		// INSERT
    		if($codeEntreprise == null){
    			// Formulaire de depot d'une entreprise
    			$formEntreprise = new Application_Form_DepotEntreprise();
    			$formEntreprise->setTranslator(Bootstrap::_initTranslate());
    			// Titre du formulaire
    			$this->view->titreForm = "Nouvelle entreprise";
    		} 
    		// UPDATE
    		else {
    			// Recupere les informations d'une entreprise
    			$uneEntreprise = $modelEntreprise->getEntreprise($codeEntreprise);
    
    			if($uneEntreprise == null){
    				$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune entreprise ne correspond.'));
    				$this->redirect('/responsable/index/');
    			} else {
    				// Recupere la liste des employés
    				$lesEmployes = $modelPersonne->getPersonneByEntreprise($codeEntreprise);
    				// Envoi le detail d'une entreprise au formulaire
    				$formEntreprise = new Application_Form_DepotEntreprise($lesEmployes, $dirigeantDeLentreprise);
    				$formEntreprise->setTranslator(Bootstrap::_initTranslate());
    				$formEntreprise->populate($uneEntreprise->toArray());
    			}
    			// Titre du formulaire
    			$this->view->titreForm = "Modification entreprise";
    		}
    
    		// Traitement du formulaire
    		// Si le formulaire a été posté
    		if($this->getRequest()->isPost()) {
    			// Recupere les informations du formulaire
    			$formData = $this->getRequest()->getPost();
    
    			// Si les informations sont valides par rapport au formulaire init (initiale)
    			if($formEntreprise->isValid($formData)) {
    				// Recupere les attributs dans des variables
    				$rsEntreprise = $formEntreprise->getValue('rsEntreprise');
    				$dirigeantEntreprise = $formEntreprise->getValue('idPersonneDirigeant'); 
    				$siretEntreprise = $formEntreprise->getValue('siretEntreprise');
    				$adrRueEntreprise = $formEntreprise->getValue('adrRueEntreprise');
    				$adrCpEntreprise = $formEntreprise->getValue('adrCpEntreprise');
    				$adrVilleEntreprise = $formEntreprise->getValue('adrVilleEntreprise');
    				$telEntreprise = $formEntreprise->getValue('telEntreprise');
    				$emailEntreprise = $formEntreprise->getValue('emailEntreprise');
    				$loginEntreprise = $formEntreprise->getValue('loginEntreprise');
    				$mdpEntreprise = $formEntreprise->getValue('mdpEntreprise');
    				
    				// INSERT
    				if($codeEntreprise == null) {
    					$idEntreprise = $modelEntreprise->insertEntreprise($rsEntreprise, $dirigeantEntreprise, $siretEntreprise, $adrRueEntreprise, $adrCpEntreprise, $adrVilleEntreprise, $telEntreprise, $emailEntreprise, $loginEntreprise, $mdpEntreprise);
    					if($idEntreprise != -1){
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'entreprise a été enregistré avec succès.'));
    						$this->redirect("/responsable/fiche/code/$idEntreprise/type/Entreprise/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de l\'entreprise.'));
    						$formEntreprise->populate($formData);
    					}
    				}
    				// UPDATE
    				else {
    					if($modelEntreprise->updateEntreprise($rsEntreprise, $dirigeantEntreprise, $siretEntreprise, $adrRueEntreprise, $adrCpEntreprise, $adrVilleEntreprise, $telEntreprise, $emailEntreprise, $loginEntreprise, $mdpEntreprise, $codeEntreprise)) {
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'entreprise a été modifié avec succès.'));
    						$this->redirect("/responsable/index/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification de l\'entreprise.'));
    						$formEntreprise->populate($formData);
    					}
    				}
    			} else $formEntreprise->populate($formData);
    		}
    
    		// Envoie a la vue le formulaire d'une entreprise
    		$this->view->formEntreprise = $formEntreprise;
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('info'=>'Aucune page de ce nom n\'a été trouvé.'));
    		$this->redirect("/index/index/");
    	}
    }
    
    /**
     * Formulaire de dépot d'une personne
     * Ajouter/Modifier
     */
    public function depotpersonneAction()
    {
    	$this->view->title = "Enregistrement d'un employé"; // Titre de la page
    
    	// Recupere le code de la personne passé en param (si exist)
    	$codePersonne = $this->getRequest()->getParam('code');
    	$codeEntreprise = $this->getRequest()->getParam('entreprise');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Personne
    	$modelPersonne = new Application_Model_DbTable_Personne();
    
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		// INSERT
    		if($codePersonne == null){
    			// Formulaire de depot d'une personne
    			$formPersonne = new Application_Form_DepotPersonne();
    			$formPersonne->setTranslator(Bootstrap::_initTranslate());
    			// Titre du formulaire
    			$this->view->titreForm = "Nouveau employé";
    		}
    		// UPDATE
    		else {
    			// Recupere les informations d'une personne
    			$unePersonne = $modelPersonne->getPersonne($codePersonne, $codeEntreprise);
    
    			if($unePersonne == null){
    				$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun employé ne correspond.'));
    				$this->redirect('/responsable/index/fiche/code/'.$codeEntreprise.'/type/Entreprise');
    			} else {
    				// Envoi le detail d'une personne au formulaire
    				$formPersonne = new Application_Form_DepotPersonne();
    				$formPersonne->setTranslator(Bootstrap::_initTranslate());
    				$formPersonne->populate($unePersonne->toArray());
    			}
    			// Titre du formulaire
    			$this->view->titreForm = "Modification employé";
    		}
    
    		// Traitement du formulaire
    		// Si le formulaire a été posté
    		if($this->getRequest()->isPost()) {
    			// Recupere les informations du formulaire
    			$formData = $this->getRequest()->getPost();
    
    			// Si les informations sont valides par rapport au formulaire init (initiale)
    			if($formPersonne->isValid($formData)) {
    				// Recupere les attributs dans des variables
    				$nomPersonne = $formPersonne->getValue('nomPersonne');
    				$prenomPersonne = $formPersonne->getValue('prenomPersonne');
    				$fonctionPersonne = $formPersonne->getValue('fonctionPersonne');
    				$telPortPersonne = $formPersonne->getValue('telPortPersonne');
    				$telPostePersonne = $formPersonne->getValue('telPostePersonne');
    				$emailPersonne = $formPersonne->getValue('emailPersonne');
    
    				// INSERT
    				if($codePersonne == null) {
    					$idPersonne = $modelPersonne->insertPersonne($codeEntreprise, $nomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne);
    					if($idPersonne != -1){
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'La personne a été enregistré avec succès.'));
    						$this->redirect("/responsable/fiche/code/$codeEntreprise/type/Entreprise/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de la personne.'));
    						$formPersonne->populate($formData);
    					}
    				}
    				// UPDATE
    				else {
    					if($modelPersonne->updatePersonne($codeEntreprise, $nomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne, $codePersonne)) {
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'La personne a été modifié avec succès.'));
    						$this->redirect("/responsable/fiche/code/$codeEntreprise/type/Entreprise/");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification de la personne.'));
    						$formPersonne->populate($formData);
    					}
    				}
    			} else $formPersonne->populate($formData);
    		}
    
    		// Envoie a la vue le formulaire d'une personne
    		$this->view->formPersonne = $formPersonne;
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('info'=>'Aucune page de ce nom n\'a été trouvé.'));
    		$this->redirect("/index/index/");
    	}
    	
    	// Envoi le code de l'entreprise a la vue
    	$this->view->codeEntreprise = $codeEntreprise;
    }
    
}







