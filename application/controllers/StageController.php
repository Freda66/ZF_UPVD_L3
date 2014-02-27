<?php

class StageController extends Zend_Controller_Action
{

    public function init()
    {
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
		if(!isset($session->infoUser)){
			// Message flash + Redirection
			$this->_helper->flashMessenger->addMessage(array('danger'=>'Pour accéder au stage, veuillez vous connecter.'));
			$this->redirect('/index/index/');
		}
    }

    public function indexAction()
    {
    	$this->view->title = "Liste des stages"; // Titre de la page
    	
    	// Crée un objet dbtable Stage et RealiserEtudiantStage
    	$modelStage = new Application_Model_DbTable_Stage();
    	$modelRealiserEtudiantStage = new Application_Model_DbTable_RealiserEtudiantStage();
    	
    	// Initialise la variable lesStages et bool isFindEtudiant 
    	$lesStages = null;
    	
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Liste des stages déposés par l'entreprise
    	if($session->infoUser->type == "Entreprise"){
    		$lesStages = $modelStage->getStagesEntreprise($session->infoUser->identifiant); // Recupere les stages
    		// Parcour les stages et recupere un bool si le stage existe dans la table realiseretudiantstage
    		for($i = 0; $i < count($lesStages); $i++){
    			$lesStages[$i]["isFindEtudiant"] = $modelRealiserEtudiantStage->isExist($lesStages[$i]["codeStage"]);
    		}
    	} 
    	// Liste des stages dont il est tuteur
    	else if($session->infoUser->type == "Enseignant"){
			// Recupere le parametre url
    		$myParam = $this->getRequest()->getParam('my');
			// Recupere les stages
			if($session->infoUser->isResponsable == 0 || $myParam == "tuteur") $lesStages = $modelRealiserEtudiantStage->getStagesTuteur($session->infoUser->identifiant); // Recupere les stages
			else $lesStages = $modelStage->getStages(); // Recupere les stages
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myParam;
			$this->view->isResponsable = $session->infoUser->isResponsable;
    	} 
    	// Liste des stages disponible + filtre possible sur les siens
    	else if($session->infoUser->type == "Etudiant"){
    		// Recupere le param de l'url
    		$myStage = $this->getRequest()->getParam('my');
    		// Recupere les stages en fonction du param
    		if($myStage == "stage" || $myStage == "demande") $lesStages = $modelRealiserEtudiantStage->getMyStages($session->infoUser->identifiant, $myStage);
    		// Recupere les stages validé
    		else $lesStages = $modelStage->getStagesAllValidORAttente(); 
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myStage;
    	}
    	
    	// Envoi a la vue les stages et le boolean isFindEtudiant
    	$this->view->lesStages = $lesStages;
    	$this->view->typeSession = $session->infoUser->type;
    }
    
	public function ficheAction()
    {
    	$this->view->title = "Stage"; // Titre de la page
    	
    	// Recupere l'id du stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	// Crée un objet dbtable Stage
    	$modelStage = new Application_Model_DbTable_Stage();
    	
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere les informations d'un stage
    	$unStage = $modelStage->getStage($codeStage, $session->infoUser);
    	
    	if($unStage == null){
    		// Message flash + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
    		$this->redirect('/stage/index/');
    	} else {
	    	// Envoi le detail d'un stage a la vue
	    	$this->view->stage = $unStage;
    	}
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







