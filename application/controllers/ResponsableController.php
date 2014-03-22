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
	    		// Crée un objet dbtable entreprise
	    		$modelEntreprise = new Application_Model_DbTable_Entreprise();
	    		// Recupere les informations d'une entreprise
	    		$unUtilisateur = $modelEntreprise->getEntreprise($codeUtilisateur);
	    		// Recupere les stages déposés
	    		$lesStages = $modelStage->getStagesEntrepriseANDRealiser($codeUtilisateur);
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
    			$formEnseignant = new Application_Form_Enseignant();
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
    				$formEnseignant = new Application_Form_Enseignant($options);
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
    
}







