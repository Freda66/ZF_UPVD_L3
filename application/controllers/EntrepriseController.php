<?php
class EntrepriseController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
    	$session = Zend_Auth::getInstance()->getStorage()->read();

    	// Non connecté ou Etudiant
    	if($session == null){
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Pour accéder à Entreprise, veuillez vous connecter.'));
    		$this->redirect('/index/index/');
    	} else if($session->infoUser->type == "Etudiant"){
    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Accès refusé.'));
    		$this->redirect('/index/index/');
    	}
    }

    public function indexAction()
    {
    	// Titre de la page
    	$this->view->title = "Entreprise";
    	
    	// Recupere la session utilisateur
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere les informations de l'utilisateur
    	$typeUtilisateur = $session->infoUser->type;
    	$codeUtilisateur = $session->infoUser->identifiant;

    	// Si c'est une entreprise, on le redirige vers sa fiche
    	if($typeUtilisateur == "Entreprise"){
	    	$codeEntreprise = $this->getRequest()->getParam('code');
	    	$this->redirect('/entreprise/liste/');
    	} 
    	// Si non on affiche la liste des entreprises 
    	else $this->redirect('/entreprise/liste/');
    }
    
    /**
     * Fiche d'une entreprise
     */
    public function ficheAction(){
    	// Titre de la page
    	$this->view->title = "Entreprise";
    	 
    	// Recupere la session utilisateur
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	 
    	// Recupere le code de l'entreprise
    	if($session->infoUser->type == "Entreprise") $codeEntreprise = $session->infoUser->identifiant;
    	else $codeEntreprise = $this->getRequest()->getParam('code');
    	 
    	$typeUtilisateur = $session->infoUser->type;
    	$codeUtilisateur = $session->infoUser->identifiant;
    	 
    	/*
    	 * FICHE ENTREPRISE
    	*/
    	if($codeEntreprise != null){
    		// Crée un objet dbTable Stage
    		$modelStage = new Application_Model_DbTable_Stage();
    		// Crée un objet dbTable Personne
    		$modelPersonne = new Application_Model_DbTable_Personne();
    		// Crée un objet dbtable entreprise
    		$modelEntreprise = new Application_Model_DbTable_Entreprise();
    		// Recupere les informations d'une entreprise
    		$unUtilisateur = $modelEntreprise->getEntreprise($codeEntreprise);
    		// Recupere les stages déposés
    		$lesStages = $modelStage->getStagesEntrepriseANDRealiser($codeEntreprise);
    	
    		// Envoi a la vue les informations
    		$this->view->unUtilisateur = $unUtilisateur;
    		$this->view->lesStages = $lesStages;
    	
    		// Si l'utilisateur connecté est l'entreprise a afficher alors on charge les employés
    		if($codeUtilisateur == $codeEntreprise && $typeUtilisateur == "Entreprise") {
    			// Recupere les employes de l'entreprise
    			$lesEmployes = $modelPersonne->getPersonneByEntreprise($codeEntreprise);
    			$this->view->lesEmployes = $lesEmployes;
    			$this->view->afficheEmployes = true;
    		} else $this->view->afficheEmployes = false;
    	} else $this->redirect('/entreprise/liste/');
    }
    
    /**
     * Liste des entreprises dont l'enseignant est tuteur
     */
    public function listeAction(){
    	// Titre de la page
    	$this->view->title = "Entreprise";
    	 
    	// Recupere la session utilisateur
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	$typeUtilisateur = $session->infoUser->type;
    	$codeUtilisateur = $session->infoUser->identifiant;
    	
    	if($typeUtilisateur == "Enseignant") {	
	    	// Crée un objet dbTable Entreprise
	    	$modelEntreprise = new Application_Model_DbTable_Entreprise();
	    	
	    	// Recupere la page courante
	    	$pageEntreprise = $this->getRequest()->getParam('pageEntreprise');
	    	if(empty($pageEntreprise))	{ $pageEntreprise = 1; }
	    	 
	    	// Recupere la liste des entreprises pour un tuteur
	    	if($session->infoUser->type == "Enseignant"){
	    		$lesEntreprises = $modelEntreprise->getListeEntrepriseByTuteur($pageEntreprise, $session->infoUser->identifiant);
	    	} //else $lesEntreprises = $modelEntreprise->getListeEntreprise($pageEntreprise);
	    	 
	    	// Envoi a la vue la liste des entreprises
	    	$this->view->lesEntreprises = $lesEntreprises;
    	} else $this->redirect('/entreprise/fiche/code/'.$codeUtilisateur);
    }
    
    
    /**
     * Formulaire de dépot d'un employe
     * Ajouter/Modifier
     */
    public function depotemployeAction()
    {
    	$this->view->title = "Enregistrement d'un employé"; // Titre de la page
    	
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    
    	// Recupere le code de la personne passé en param (si exist)
    	$codePersonne = $this->getRequest()->getParam('code');
    	$codeEntreprise = $session->infoUser->identifiant;
    	
    	// Crée un objet dbtable Personne
    	$modelPersonne = new Application_Model_DbTable_Personne();
    
    	if($session->infoUser->type == "Entreprise"){
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
    				$this->redirect('/entreprise/index/code/'.$codeEntreprise);
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
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'employé a été enregistré avec succès.'));
    						$this->redirect("/entreprise/index/code/$codeEntreprise");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de l\'insertion de l\'employé.'));
    						$formPersonne->populate($formData);
    					}
    				}
    				// UPDATE
    				else {
    					if($modelPersonne->updatePersonne($codeEntreprise, $nomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne, $codePersonne)) {
    						// Message + Redirection
    						$this->_helper->flashMessenger->addMessage(array('success'=>'L\'employé a été modifié avec succès.'));
    						$this->redirect("/entreprise/index/code/$codeEntreprise");
    					} else {
    						$this->_helper->flashMessenger->addMessage(array('danger'=>'Une erreur est survenu lors de la modification de l\'employé.'));
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
    
	/**
     * Supprime un employe (passe son etat a -1)
     */
    public function deleteAction(){
    	// Recupere la session en cours
    	$session = Zend_Auth::getInstance()->getStorage()->read();
    	
    	// Recupere le code de la personne passé en param (si exist)
    	$codePersonne = $this->getRequest()->getParam('code');
    	$codeEntreprise = $session->infoUser->identifiant;

    	// Crée un objet dbTable Personne
    	$modelPersonne = new Application_Model_DbTable_Personne();
    	
    	// Verifie que la personne fait parti de l'entreprise
    	$unePersonne = $modelPersonne->getPersonne($codePersonne, $codeEntreprise);
    	if($unePersonne->idPersonne == $codePersonne) {
	    	// Supprime l'enregistrement, si error (dependance dans d'autre table) => passe son etat a -1
	    	$modelPersonne->deletePersonne($codePersonne, $codeEntreprise);
	    	$this->_helper->flashMessenger->addMessage(array('success'=>'L\'enregistrement a été supprimé.'));
    	} else $this->_helper->flashMessenger->addMessage(array('danger'=>'L\'enregistrement ne peut pas être supprimé.'));

    	// Redirection
    	$this->redirect('/entreprise/index/');
    }
    
}