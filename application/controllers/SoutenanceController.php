<?php

class SoutenanceController extends Zend_Controller_Action
{

	/**
	 * Init au chargement du controller
	 */
    public function init()
    {
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
		if(!isset($session->infoUser)){
			// Message flash + Redirection
			$this->_helper->flashMessenger->addMessage(array('danger'=>'Pour accéder au soutenance, veuillez vous connecter.'));
			$this->redirect('/index/index/');
		}
    }

    /**
     * Liste des soutenances
     * Différente vue en fonction de l'utilisateur
     */
    public function indexAction()
    {
    	$this->view->title = "Liste des soutenances"; // Titre de la page
    	
    	// Crée un objet dbtable Soutenance et Formation
    	$modelSoutenance = new Application_Model_DbTable_Soutenance();
    	$modelFormation = new Application_Model_DbTable_Formation();

    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Pagination
    	$page = 		$this->_request->getParam('page');
    	$formation = 	$this->_request->getParam('formation');
    	$myParam = 		$this->_request->getParam('my');
    	if(empty($page)){ $page=1; }
    	
		// Recupere les soutenances
		$lesSoutenances = $modelSoutenance->getSoutenances($page, $formation, $session->infoUser, $myParam);

    	// Envoi a la vue le param de l'url
    	$this->view->param = $myParam;
		$this->view->isResponsable = $session->infoUser->isResponsable;
    	// Envoi a la vue les formations, stages
    	$this->view->lesFormations = $modelFormation->getFormations();
    	$this->view->lesSoutenances = $lesSoutenances;
    	$this->view->typeSession = $session->infoUser->type;
    	$this->view->formation = $formation;
    }
    
    /**
     * Fiche détaillée d'une soutenance
     */
	public function ficheAction()
    {
    	$this->view->title = "Soutenance"; // Titre de la page
    	
    	// Recupere l'id de la soutenance
    	$codeSoutenance = $this->getRequest()->getParam('code');
    	
    	if($codeSoutenance != null){
	    	// Crée un objet dbtable Soutenance et SoutenanceJury
	    	$modelSoutenance = new Application_Model_DbTable_Soutenance();
			$modelSoutenanceJury = new Application_Model_DbTable_SoutenanceJury();
	    	
	    	// Recupere la session
	    	$session = Zend_Auth::getInstance()->getStorage()->read();
	    	
	    	// Recupere les informations d'une soutenance
	    	$uneSoutenance = $modelSoutenance->getSoutenanceById($codeSoutenance, $session->infoUser);
	    	// Recupere la liste des personnes succeptible d'etre jury
	    	$jurys = $modelSoutenanceJury->getSoutenanceJury($codeSoutenance);
	    	
	    	if($uneSoutenance == null){
	    		// Message flash + Redirection
	    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune soutenance ne correspond.'));
	    		$this->redirect('/soutenance/index/');
	    	} else {
		    	// Envoi le detail d'une soutenance a la vue
		    	$this->view->soutenance = $uneSoutenance;
		    	$this->view->jurys = $jurys;
	    	}
	    	
	    	$this->view->typeSession = $session->infoUser->type;
	    	if($session->infoUser->type == "Enseignant") $this->view->isResponsable = $session->infoUser->isResponsable;
    	} else {
    		// Message flash + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune soutenance ne correspond.'));
    		$this->redirect('/soutenance/index/');
    	}
    }
    
    /**
     * Formulaire de dépot d'une soutenance
     * Ajouter/Modifier
     */
    public function depotAction()
    {
    	$this->view->title = "Depot d'une soutenance"; // Titre de la page
    	
    	// Recupere le code du stage passé en param (si exist)
    	$codeSoutenance = $this->getRequest()->getParam('code');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Stage
    	$modelSoutenance = new Application_Model_DbTable_Soutenance();
    	
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true) {
	    	if($codeSoutenance == null){
		    	// Formulaire de depot d'un stage
	    		$formDepotSoutenance = new Application_Form_DepotSoutenance();
	    		$formDepotSoutenance->setTranslator(Bootstrap::_initTranslate());
	    	} else {
	    		// Recupere les informations d'un stage
	    		$uneSoutenance = $modelSoutenance->getSoutenanceById($idSoutenance, $session->infoUser);
	    			
	    		if($uneSoutenance == null){
	    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune soutenance ne correspond.'));
	    			$this->redirect('/soutenance/index/');
	    		} else {
	    			// Envoi le detail d'une soutenance a la vue
	    			$formDepotSoutenance = new Application_Form_DepotSoutenance();
	    			$formDepotSoutenance->setTranslator(Bootstrap::_initTranslate());
	    			$formDepotSoutenance->populate($uneSoutenance->toArray());
	    		}
	    	}
	    	
	    	// Traitement du formulaire
	    	// Si le formulaire a été posté
	    	if($this->getRequest()->isPost()) {
	    		// Recupere les informations du formulaire
	    		$formData = $this->getRequest()->getPost();
	    	
	    		// Si les informations sont valides par rapport au formulaire init (initiale)
	    		if($formDepotSoutenance->isValid($formData)) {
	    			// Recupere les attributs dans des variables
	    			$dateSoutenance = $formDepotSoutenance->getValue('dateSoutenance');
	    			$salleSoutenance = $formDepotSoutenance->getValue('salleSoutenance');
	    			
	    			// Insert
	    			if($codeSoutenance == null) {
	    				if($modelSoutenance->insertSoutenance($dateSoutenance, $salleSoutenance)){
	    					// Message + Redirection
	    					$this->_helper->flashMessenger->addMessage(array('success'=>'La soutenance a été déposé avec succès.'));
	    					$this->redirect("/soutenance/index/");
	    				} else {
	    					$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de la soutenance.'));
	    					$formDepotSoutenance->populate($formData);
	    				}
	    			}
	    			// Update
	    			else {
	    				if($modelSoutenance->updateStage($codeSoutenance, $dateSoutenance, $salleSoutenance)) {
	    					// Message + Redirection
	    					$this->_helper->flashMessenger->addMessage(array('success'=>'La soutenance a été modifié avec succès.'));
	    					$this->redirect("/stage/index/");
	    				} else {
	    					$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification de la soutenance.'));
	    					$formDepotSoutenance->populate($formData);
	    				}
	    			}
	    		} else $formDepotSoutenance->populate($formData);
	    	}
	    	
	    	// Envoie a la vue le formulaire de depot de stage
	    	$this->view->formDepotSoutenance = $formDepotSoutenance;
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('info'=>'Aucune page de ce nom n\'a été trouvé.'));
    		$this->redirect("/index/index/");
    	}
    }
    
    
    /**
     * Supprime une soutenance
     */
    public function deleteAction(){
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere les params
    	$idSoutenance = $this->getRequest()->getParam('codeSoutenance');
    	$idPersonne = $this->getRequest()->getParam('idPersonne');
    	$idEnseignant = $this->getRequest()->getParam('idEnseignant');
    	
    	// Si c'est un enseignant responsable
    	if(($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
    		// Cree un objet dbTable SoutenanceJury
    		$modelSoutenanceJury = new Application_Model_DbTable_SoutenanceJury();
    	
    		// Retire le stage de la bdd si il est refusé ou en attente
    		if($modelSoutenanceJury->deleteSoutenance($idSoutenance, $idPersonne, $idEnseignant)) $this->_helper->flashMessenger->addMessage(array('success'=>'La composition a été supprimé.'));
    		else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la suppression de la composition.'));
    	
    		// Redirection
    		$this->redirect("/soutenance/fiche/code/$idSoutenance");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas accéder a cette fonctionnalité.'));
    		$this->redirect("/soutenance/fiche/code/$idSoutenance");
    	}
    }
    
}







