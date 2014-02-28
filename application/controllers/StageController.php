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
    	$formation = $this->_request->getParam('formation');
    	if(empty($page)){ $page=1; }
    	
    	// Liste des stages déposés par l'entreprise
    	if($session->infoUser->type == "Entreprise"){
    		$lesStages = $modelStage->getStagesEntreprise($session->infoUser->identifiant, $formation, $page); // Recupere les stages
    		// Parcour les stages et recupere un bool si le stage existe dans la table realiseretudiantstage
    		$stageAffect = Array();
    		$i = 0;
    		foreach($lesStages as $unStage) {
    			$stageAffect[$i]["isFindEtudiant"] = $modelRealiserEtudiantStage->isExist($unStage["codeStage"]);
    			$i++;
    		}
    		$this->view->stageAffect = $stageAffect;
    	} 
    	// Liste des stages dont il est tuteur
    	else if($session->infoUser->type == "Enseignant"){
			// Recupere le parametre url
    		$myParam = $this->getRequest()->getParam('my');
			// Recupere les stages
			if($session->infoUser->isResponsable == 0 || $myParam == "tuteur") $lesStages = $modelRealiserEtudiantStage->getStagesTuteur($session->infoUser->identifiant, $formation, $page); // Recupere les stages
			else $lesStages = $modelStage->getStages($page, $formation); // Recupere les stages
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myParam;
			$this->view->isResponsable = $session->infoUser->isResponsable;
    	} 
    	// Liste des stages disponible + filtre possible sur les siens
    	else if($session->infoUser->type == "Etudiant"){
    		// Recupere le param de l'url
    		$myStage = $this->getRequest()->getParam('my');
    		// Recupere les stages en fonction du param
    		if($myStage == "stage" || $myStage == "demande") $lesStages = $modelRealiserEtudiantStage->getMyStages($session->infoUser->identifiant, $myStage, $formation, $page);
    		// Recupere les stages validé
    		else $lesStages = $modelStage->getStagesAllValidORAttente(1, $formation, $page); 
    		// Envoi a la vue le param de l'url
    		$this->view->param = $myStage;
    	}
    	
    	// Envoi a la vue les formations, stages et le boolean isFindEtudiant
    	$this->view->lesFormations = $modelFormation->getFormations();
    	$this->view->lesStages = $lesStages;
    	$this->view->typeSession = $session->infoUser->type;
    	$this->view->formation = $formation;
    }
    
	public function ficheAction()
    {
    	$this->view->title = "Stage"; // Titre de la page
    	
    	// Recupere l'id du stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	if($codeStage != null){
	    	// Crée un objet dbtable Stage
	    	$modelStage = new Application_Model_DbTable_Stage();
	    	
	    	// Recupere la session
	    	$session = Zend_Auth::getInstance()->getStorage()->read();
	    	
	    	// Recupere les informations d'un stage
	    	$unStage = $modelStage->getStage($codeStage, $session->infoUser);
	    	
	    	if($unStage == null || ($session->infoUser->type == "Etudiant" && $unStage->etatStage != 1)){
	    		// Message flash + Redirection
	    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
	    		$this->redirect('/stage/index/');
	    	} else {
		    	// Envoi le detail d'un stage a la vue
		    	$this->view->stage = $unStage;
	    	}
	    	
	    	$this->view->typeSession = $session->infoUser->type;
	    	if($session->infoUser->type == "Enseignant") $this->view->isResponsable = $session->infoUser->isResponsable;
    	} else {
    		// Message flash + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Aucun stage ne correspond.'));
    		$this->redirect('/stage/index/');
    	}
    }
    
    public function depotAction()
    {
    	$this->view->title = "Depot d'un stage"; // Titre de la page
    	
    	// Recupere le code du stage passé en param (si exist)
    	$codeStage = $this->getRequest()->getParam('code');
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Crée un objet dbtable Stage
    	$modelStage = new Application_Model_DbTable_Stage();
    	
    	if($session->infoUser->type == "Entreprise" || ($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
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
	    				if($modelStage->updateStage($libelleStage, $dateDebutStage, $dateFinStage, $idTuteur, $descriptionStage, $session->infoUser->identifiant, $codeStage)) {
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
    
    public function demandeAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    	
    	// Si c'est un étudiant 
    	if($session->infoUser->type == "Etudiant"){
    		// Cree un objet dbTable RealiserEtudiantStage
    		$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
			// Fait la demande de stage pour l'etudiant    		
			if($modelRES->insertRES($session->infoUser->identifiant, $codeStage)){
				$this->_helper->flashMessenger->addMessage(array('success'=>'Votre demande de stage a été enregistré, elle est en attente de validation par un enseignant responsable.'));
			} else {
				$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de l\'insertion de la demande de stage.'));
			} 
			$this->redirect("/stage/fiche/code/$codeStage");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas faire de demande de stage.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
    public function canceldemandeAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    	 
    	// Si c'est un étudiant ou enseignant responsable
    	if($session->infoUser->type == "Etudiant" || ($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true)){
    		// Cree un objet dbTable RealiserEtudiantStage
    		$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
    		// Annule la demande de stage pour l'etudiant
    		if($session->infoUser->type == "Enseignant"){
    			if($modelRES->deleteRES($codeStage)){
    				$this->_helper->flashMessenger->addMessage(array('success'=>'Votre annulation de demande de stage a été prise en compte.'));
    			} else {
    				$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de l\'annulation de la demande de stage.'));
    			}
    		} else {
	    		if($modelRES->deleteRES($codeStage, $session->infoUser->identifiant)){
	    			$this->_helper->flashMessenger->addMessage(array('success'=>'Votre annulation de demande de stage a été prise en compte.'));
	    		} else {
	    			$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de l\'annulation de la demande de stage.'));
	    		}
    		}
    		$this->redirect("/stage/fiche/code/$codeStage");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas annuler de demande de stage.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
    public function updateetatAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    	// Recupere l'etat du stage
    	$etatStage = $this->getRequest()->getParam('etat');
    
    	// Si c'est un enseignant responsable
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		// Cree un objet dbTable Stage
    		$modelStage = new Application_Model_DbTable_Stage();
    		
    		// Active le stage
    		if($etatStage == 1){
    			if($modelStage->updateEtat($codeStage, 1)) $this->_helper->flashMessenger->addMessage(array('success'=>'Le stage a été activé.'));
    			else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la modification de l\'etat du stage.')); 
    		} 
    		// Desactive le stage
    		else if($etatStage == -1){
    			if($modelStage->updateEtat($codeStage, -1)) $this->_helper->flashMessenger->addMessage(array('success'=>'Le stage a été désactivé.'));
    			else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la modification de l\'etat du stage.'));
    		}
    		
    		// Redirection
    		$this->redirect("/stage/fiche/code/$codeStage");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas modifier l\'etat du stage.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
    public function retireretudiantAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    	// Recupere le code etudiant
    	$codeEtudiant = $this->getRequest()->getParam('etudiant');
    
    	// Si c'est un enseignant responsable
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		// Cree un objet dbTable Stage
    		$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
    
    		// Retire l'etudiant
    		if($modelRES->deleteRESByResponsable($codeStage, $codeEtudiant)) $this->_helper->flashMessenger->addMessage(array('success'=>'L\'etudiant a été retiré.'));
    		else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la suppression de l\'etudiant.'));
    
	    	$this->redirect("/stage/fiche/code/$codeStage");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas accéder a cette fonctionnalité.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
    public function retirerenseignantAction()
    {
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	// Recupere le code stage
    	$codeStage = $this->getRequest()->getParam('code');
    
    	// Si c'est un enseignant responsable
    	if($session->infoUser->type == "Enseignant" && $session->infoUser->isResponsable == true){
    		// Cree un objet dbTable Stage
    		$modelRES = new Application_Model_DbTable_RealiserEtudiantStage();
    
    		// Retire l'enseignant tuteur
    		if($modelRES->retirerenseignant($codeStage)) $this->_helper->flashMessenger->addMessage(array('success'=>'L\'enseignant tuteur a été retiré.'));
    		else $this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur s\'est produite lors de la suppression de l\'enseignant tuteur.'));
    
    		$this->redirect("/stage/fiche/code/$codeStage");
    	} else {
    		// Message + Redirection
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Vous ne pouvez pas accéder a cette fonctionnalité.'));
    		$this->redirect("/stage/fiche/code/$codeStage");
    	}
    }
    
}







