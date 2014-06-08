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
    	
    	// Crée un objet dbtable Stage, RealiserEtudiantStage et Formation
    	$modelStage = new Application_Model_DbTable_Stage();
    	$modelRealiserEtudiantStage = new Application_Model_DbTable_RealiserEtudiantStage();
    	$modelFormation = new Application_Model_DbTable_Formation();
    	
    	// Initialise la variable lesStages et bool isFindEtudiant 
    	$lesStages = null;
    	
    	// Recupere la session
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Pagination
    	$page = $this->_request->getParam('page');
    	if(empty($page)){ $page=1; }
    	$formation = $this->_request->getParam('formation');
    	$etat = $this->_request->getParam('etat');
    	
    	// Liste des stages déposés par l'entreprise
    	if($session->infoUser->type == "Entreprise"){
    		$lesStages = $modelStage->getStagesEntreprise($session->infoUser->identifiant, $formation, $page); // Recupere les stages
    	} 
    	// Liste des stages dont il est tuteur
    	else if($session->infoUser->type == "Enseignant"){
			// Recupere le parametre url
    		$myParam = $this->getRequest()->getParam('my');
			// Recupere les stages
			if($session->infoUser->isResponsable == 0 || $myParam == "tuteur") $lesStages = $modelRealiserEtudiantStage->getStagesTuteur($session->infoUser->identifiant, $formation, $page); // Recupere les stages
			else $lesStages = $modelStage->getStages($page, $formation, $etat); // Recupere les stages
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myParam;
			$this->view->isResponsable = $session->infoUser->isResponsable;
    	} 
    	// Liste des stages disponible + filtre possible sur les siens
    	else if($session->infoUser->type == "Etudiant"){
    		$modelDemandeEtudiantStage = new Application_Model_DbTable_DemandeEtudiantStage();
    		$modelEtudiant = new Application_Model_DbTable_Etudiant();
    		// Recupere les informations de l'etudiant
    		$unEtudiant = $modelEtudiant->getEtudiant($session->infoUser->identifiant);
    		// Recupere le param de l'url
    		$myStage = $this->getRequest()->getParam('my');
    		// Recupere les stages en fonction du param
    		if($myStage == "stage") $lesStages = $modelRealiserEtudiantStage->getMyStages($session->infoUser->identifiant, $myStage, $formation, $page);
    		else if($myStage == "demande") $lesStages = $modelDemandeEtudiantStage->getMyStages($session->infoUser->identifiant, $formation, $page);
    		// Recupere les stages validé
    		else $lesStages = $modelStage->getStagesAllValidORAttente(1, $unEtudiant->idFormation, $page); 
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myStage;
    	}
    	
    	// Parcour les stages et recupere un bool si le stage existe dans la table realiseretudiantstage
    	$stageAffect = Array();
    	$i = 0;
    	foreach($lesStages as $unStage) {
    		$stageAffect[$i]["isFindEtudiant"] = $modelRealiserEtudiantStage->isExist($unStage["codeStage"]);
    		$i++;
    	}
    	$this->view->stageAffect = $stageAffect;
    	
    	// Envoi a la vue les formations, stages et le boolean isFindEtudiant
    	$this->view->lesFormations = $modelFormation->getFormations();
    	$this->view->lesStages = $lesStages;
    	$this->view->typeSession = $session->infoUser->type;
    	$this->view->formation = $formation;
    }
    
    /**
     * Fiche détaillée d'une soutenance
     */
	public function ficheAction()
    {
    	$this->view->title = "Soutenance"; // Titre de la page
    	
    	// Recupere l'id du stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	if($codeStage != null){
	    	// Crée un objet dbtable Stage
	    	$modelStage = new Application_Model_DbTable_Stage();
	    	$modelDES = new Application_Model_DbTable_DemandeEtudiantStage();
	    	
	    	// Recupere la session
	    	$session = Zend_Auth::getInstance()->getStorage()->read();
	    	
	    	// Recupere les informations d'un stage
	    	$unStage = $modelStage->getStage($codeStage, $session->infoUser);
	    	// Recupere les demande du stage
	    	$lesDemandes = $modelDES->getListeDemandeStage($codeStage);
	    	
	    	if($unStage == null || ($session->infoUser->type == "Etudiant" && $unStage->etatStage != 1)){
	    		// Message flash + Redirection
	    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
	    		$this->redirect('/stage/index/');
	    	} else {
		    	// Envoi le detail d'un stage a la vue
		    	$this->view->stage = $unStage;
		    	$this->view->lesDemandes = $lesDemandes;
	    	}
	    	
	    	$this->view->typeSession = $session->infoUser->type;
	    	if($session->infoUser->type == "Enseignant") $this->view->isResponsable = $session->infoUser->isResponsable;
    	} else {
    		// Message flash + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
    		$this->redirect('/stage/index/');
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
    	$codeStage = $this->getRequest()->getParam('code');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Stage
    	$modelStage = new Application_Model_DbTable_Stage();
    	
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true) {
	    	if($codeStage == null){
		    	// Formulaire de depot d'un stage
	    		$formDepotStage = new Application_Form_DepotStage();
	    		$formDepotStage->setTranslator(Bootstrap::_initTranslate());
	    	} else {
	    		// Recupere les informations d'un stage
	    		$unStage = $modelStage->getStage($codeStage, $session->infoUser);
	    			
	    		if($unStage == null){
	    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
	    			$this->redirect('/stage/index/');
	    		} else if($unStage->etatStage == 1){
	    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Le stage ne peut pas être modifié.'));
	    			$this->redirect('/stage/index/');
	    		} else {
	    			// Envoi le detail d'un stage a la vue
	    			$formDepotStage = new Application_Form_DepotStage();
	    			$formDepotStage->setTranslator(Bootstrap::_initTranslate());
	    			$formDepotStage->populate($unStage->toArray());
	    		}
	    	}
	    	
	    	// Traitement du formulaire
	    	// Si le formulaire a été posté
	    	if($this->getRequest()->isPost()) {
	    		// Recupere les informations du formulaire
	    		$formData = $this->getRequest()->getPost();
	    	
	    		// Si les informations sont valides par rapport au formulaire init (initiale)
	    		if($formDepotStage->isValid($formData)) {
	    			// Recupere les attributs dans des variables
	    			$libelleStage = $formDepotStage->getValue('libelleStage');
	    			$dateDebutStage = $formDepotStage->getValue('dateDebutStage');
	    			$dateFinStage = $formDepotStage->getValue('dateFinStage');
	    			$idTuteur = $formDepotStage->getValue('idTuteur');
	    			$descriptionStage = $formDepotStage->getValue('descriptionStage');
	    			
	    			// Insert
	    			if($codeStage == null) {
	    				if($modelStage->insertStage($libelleStage, $dateDebutStage, $dateFinStage, $idTuteur, $descriptionStage, $session->infoUser->identifiant)){
	    					// Message + Redirection
	    					$this->_helper->flashMessenger->addMessage(array('success'=>'Le stage a été déposé avec succès.'));
	    					$this->redirect("/stage/index/");
	    				} else {
	    					$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion du stage.'));
	    					$formDepotStage->populate($formData);
	    				}
	    			}
	    			// Update
	    			else {
	    				if($modelStage->updateStage($libelleStage, $dateDebutStage, $dateFinStage, $idTuteur, $descriptionStage, $session->infoUser->identifiant, $codeStage, $unStage->etatStage)) {
	    					// Message + Redirection
	    					$this->_helper->flashMessenger->addMessage(array('success'=>'Le stage a été modifié avec succès.'));
	    					$this->redirect("/stage/index/");
	    				} else {
	    					$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification du stage.'));
	    					$formDepotStage->populate($formData);
	    				}
	    			}
	    		} else $formDepotStage->populate($formData);
	    	}
	    	
	    	// Envoie a la vue le formulaire de depot de stage
	    	$this->view->formDepotStage = $formDepotStage;
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
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	// Si c'est un enseignant responsable
    	if(($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
    		// Cree un objet dbTable Stage
    		$modelStage = new Application_Model_DbTable_Stage();
    	
    		// Recupere les informations du stage
    		$unStage = $modelStage->getStage($codeStage, $session);
    		
    		// Retire le stage de la bdd si il est refusé ou en attente
    		if($unStage->etatStage != 1 && $modelStage->deleteStage($codeStage)) $this->_helper->flashMessenger->addMessage(array('success'=>'Le stage a été supprimé.'));
    		else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la suppression du stage.'));
    	
    		// Redirection
    		$this->redirect("/stage/");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas accéder a cette fonctionnalité.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
}






