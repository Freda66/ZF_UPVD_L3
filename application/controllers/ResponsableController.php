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
    
}







