<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
        //var_dump(Zend_Auth::getInstance()->getStorage()->read());
    }

    public function indexAction()
    {
    	$this->view->title = "Accueil"; // Titre de la page
    }
    
	public function mcdAction()
    {
    	$this->view->title = "Modèle conceptuel des données"; // Titre de la page
    }
    
    public function mldAction()
    {
    	$this->view->title = "Modèle logique des données"; // Titre de la page
    }
    
    public function connexionAction()
    {
    	if(isset($_POST['submitConnexion'])){
    		// Instancie la class metier connexion
    		$modelUtilisater = new Application_Model_Metier_Utilisateur();
    		 
    		// Appel la fonction qui retourne true s'il est connecté et false s'il ne l'est pas
    		$isConnect = $modelUtilisater->connexionUser($_POST['login'], $_POST['mdp']);
    		
    		if($isConnect){
    			// Message Flash Success
    			$this->_helper->flashMessenger->addMessage(array('success'=>'Vous êtes connecté.'));
    		} else {
				// Message Flash Error
	    		$this->_helper->flashMessenger->addMessage(array('danger'=>'Couple "Identifiant/Mot de passe" incorrect.'));
    		}
    		
    		// Redirection
    		$this->redirect('/index/index/');
    	} else {
    		// Message Flash
    		$this->_helper->flashMessenger->addMessage(array('info'=>'Veuillez utiliser le formulaire de connexion.'));
    		// Redirection
    		$this->redirect('/index/index/');
    	}
    }
    
    public function deconnexionAction(){
    	// Vide la session
    	Zend_Auth::getInstance()->clearIdentity();
    	// Redirection vers l'index
    	$this->redirect('/index/index/');
    }
	
}







