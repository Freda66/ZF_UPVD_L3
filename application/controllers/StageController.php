<?php

class StageController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
        var_dump(Zend_Auth::getInstance()->getStorage()->read());
    }

    public function indexAction()
    {
    	$this->view->title = "Liste des stages"; // Titre de la page
    	
    	// Crée un objet dbtable Stage
    	$modelStage = new Application_Model_DbTable_Stage();
		// Crée un objet dbtable Stage realiser 
    	//$modelStageRealiser = new Application_Model_DbTable_StageRealiser();
    	
    	// Initialise la variable lesStages 
    	$lesStages = null;
    	
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Liste des stages déposés par l'entreprise
    	if($session->infoUser->type == "Entreprise"){
    		$lesStages = $modelStage->getStagesEntreprise($session->infoUser->identifiant); // Recupere les stages
    	} 
    	// Liste des stages dont il est tuteur
    	else if($session->infoUser->type == "Enseignant"){
    		//$lesStages = $modelStageRealiser->getStagesTuteur($session->infoUser->identifiant); // Recupere les stages
    	} 
    	// Liste des stages disponible + filtre possible sur les siens
    	else if($session->infoUser->type == "Etudiant"){
    		$lesStages = $modelStage->getStagesAllValidORAttente(); // Recupere les stages
    	}
    	
    	// Envoi a la vue les stages
    	$this->view->lesStages = $lesStages;
    }
    
	public function ficheAction()
    {
    	$this->view->title = "Stage"; // Titre de la page
    	
    	// Recupere l'id du stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	// Crée un objet dbtable Stage
    	$modelStage = new Application_Model_DbTable_Stage();
    	
    	// Recupere les informations d'un stage
    	$this->view->stage = $modelStage->getStage($codeStage);
    }
    
    public function depotAction()
    {
    	$this->view->title = "Depot d'un stage"; // Titre de la page
    	
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	if($codeStage == null){
	    	// Formulaire de depot d'un stage
	    	
    	} else {
    		// Recupere le stage
    		
    		// Formulaire de modification d'un stage
    		
    	}
    }
    
    public function demanderAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->ge-tStorage()->read();
    	
    	// Si c'est un étudiant 
    	if($session->type == "Etudiant"){
    		// Recupere le code du stage
    		$codeStage = $this->getRequest()->getParam('code');
    		
			// Fait la demande de stage pour l'etudiant    		
			
    	}
    }
    
}







