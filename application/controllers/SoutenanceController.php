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
	    	$jurys = $modelSoutenanceJury->getSoutenanceJury($codeSoutenance, $session->infoUser);
	    	
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
	    	$this->view->identifiant = $session->infoUser->identifiant;
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
	    		// Envoi a la vue que c'est un insert
	    		$this->view->libelleTitre = "Dépot";
	    	} else {
	    		// Recupere les informations d'une soutenance
	    		$uneSoutenance = $modelSoutenance->getSoutenanceForm($codeSoutenance);
	    			
	    		if($uneSoutenance == null){
	    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucune soutenance ne correspond.'));
	    			$this->redirect('/soutenance/index/');
	    		} else {
	    			// Format d'affichage DateTime Picker
	    			$uneSoutenance->dateSoutenance = str_replace('-', '/', $uneSoutenance->dateSoutenance);
	    			// Envoi le detail d'une soutenance a la vue
	    			$formDepotSoutenance = new Application_Form_DepotSoutenance($uneSoutenance->idStage);
	    			$formDepotSoutenance->setTranslator(Bootstrap::_initTranslate());
	    			$formDepotSoutenance->populate($uneSoutenance->toArray());
	    			// Envoi a la vue que c'est un update
	    			$this->view->libelleTitre = "Modification";
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
	    			$idStage = $formDepotSoutenance->getValue('idStage');
	    			$dateSoutenance = $formDepotSoutenance->getValue('dateSoutenance');
	    			$salleSoutenance = $formDepotSoutenance->getValue('salleSoutenance');
	    			
	    			// Insert
	    			if($codeSoutenance == null) {
	    				// Insert la soutenance 
	    				$errorInsert = false;
	    				$idSoutenance = $modelSoutenance->insertSoutenance($dateSoutenance, $salleSoutenance);
	    				if($idSoutenance != null || $idSoutenance != 0){
	    					$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
	    					if($modelRES->updateSoutenance($idSoutenance, $idStage)){
		    					// Message + Redirection
		    					$this->_helper->flashMessenger->addMessage(array('success'=>'La soutenance a été déposé avec succès.'));
		    					$this->redirect("/soutenance/index/");
	    					} else $errorInsert = true;
	    				} else $errorInsert = true;
	    				
	    				if($errorInsert){ 
	    					$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de la soutenance.'));
	    					$formDepotSoutenance->populate($formData);
	    				}
	    			}
	    			// Update
	    			else {
	    				if($modelSoutenance->updateSoutenance($codeSoutenance, $dateSoutenance, $salleSoutenance)) {
	    					// Message + Redirection
	    					$this->_helper->flashMessenger->addMessage(array('success'=>'La soutenance a été modifié avec succès.'));
	    					$this->redirect("/soutenance/fiche/code/$codeSoutenance");
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
     * Liste des enseignats et des personnes par entreprise qui peuvent etre selectionner pour participier au jury
     */
    public function ajouterjuryAction(){
    	$this->view->title = "Sélection d'un membre du jury"; // Titre de la page

    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere les params
    	$idSoutenance = $this->getRequest()->getParam('codeSoutenance');
    	$idJuryPersonne = $this->getRequest()->getParam('codePersonne');
    	$idJuryEnseignant = $this->getRequest()->getParam('codeEnseignant');
    	
    	// Si c'est un enseignant responsable
    	if(($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
    		// Affiche la liste des jurys a selectionner pour une soutenance
    		if($idJuryPersonne == "" && $idJuryEnseignant == ""){
		    	// Objet model dbTable (enseignant, entreprise)
		    	$modelEnseignant = new Application_Model_DbTable_Enseignant();
		    	$modelEntreprise = new Application_Model_DbTable_Entreprise();
		
		    	// Recupere la liste des utilisateurs
		    	$lesEnseignants = $modelEnseignant->getListeEnseignantSoutenance($idSoutenance);
		    	$lesEntreprises = $modelEntreprise->getListeEntreprise();
		    	 
		    	$this->view->lesEnseignants = $lesEnseignants;
		    	$this->view->lesEntreprises = $lesEntreprises;
		    	$this->view->idSoutenance = $idSoutenance;
    		} 
    		//
    		else {
    			// Ajout d'une composition
    			$modelSoutenanceJury = new Application_Model_DbTable_SoutenanceJury();
    			if($modelSoutenanceJury->insertSoutenanceJury($idSoutenance, $idJuryPersonne, $idJuryEnseignant)){
    				$this->_helper->flashMessenger->addMessage(array('success'=>'La composition a été ajoutée.'));
    				$this->redirect("/soutenance/fiche/code/$idSoutenance");
    			} else {
    				$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'enregistrement de la composition.'));
    				$this->redirect("/soutenance/fiche/code/$idSoutenance");
    			}
    		}
    	} else {
    	}
    }
    
    /**
     * Supprime une soutenance
     */
    public function deletesoutenanceAction(){
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	 
    	// Recupere les params
    	$idSoutenance = $this->getRequest()->getParam('code');
    	 
    	// Si c'est un enseignant responsable
    	if(($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
    		// Cree un objet dbTable Soutenance
    		$modelSoutenance = new Application_Model_DbTable_Soutenance();
    		$modelSoutenanceJury = new Application_Model_DbTable_SoutenanceJury();
    		$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
    		 
    		$errorDelete = false;
    		// Retire l'id de soutenance de Realiser Etudiant Stage
    		if($modelRES->deleteSoutenance($idSoutenance)){
    			// Supprime les compositions de jury de la soutenance
    			$modelSoutenanceJury->delete("codeSoutenance = ".(int)$idSoutenance);
   				// Supprime la soutenance
	    		if($modelSoutenance->deleteSoutenance($idSoutenance)) $this->_helper->flashMessenger->addMessage(array('success'=>'La soutenance a été supprimé.'));
	    		else $errorDelete = true;
    		} else $errorDelete = true;
    		if($errorDelete) $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la suppression de la soutenance.'));
    		 
    		// Redirection
    		$this->redirect("/soutenance/index/");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas accéder a cette fonctionnalité.'));
    		$this->redirect("/soutenance/fiche/code/$idSoutenance");
    	}
    }
    
    /**
     * Supprime une composition de jury
     */
    public function deletejuryAction(){
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere les params
    	$idSoutenance = $this->getRequest()->getParam('codeSoutenance');
    	$idPersonne = $this->getRequest()->getParam('idPersonne');
    	$idEnseignant = $this->getRequest()->getParam('idEnseignant');
    	
    	// Si c'est un enseignant responsable
    	if($session->infoUser->type != "Etudiant"){
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







