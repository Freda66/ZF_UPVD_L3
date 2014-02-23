<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
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
    		// Message Flash Success
    		if($_POST['login'] == "frederic.cano.66@gmail.com"){
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
	
}







