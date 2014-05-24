<?php

class EntrepriseController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
        var_dump(Zend_Auth::getInstance()->getStorage()->read());
    }

    public function indexAction()
    {
    	$this->view->title = "Entreprise"; // Titre de la page
    	
    	// Liste des salariés de l'entreprise
    	// + liste des stages déposés
    	
    	// Btn ou onglet vers
    	// Formulaire de dépot de stage
    	// Formulaire d'ajout de salarié
    }
    
	public function salarieAction()
    {
    	$this->view->title = "Salarié"; // Titre de la page
    	
    	// Fiche formulaire (bloqué) mais déblocable d'ajout/modification d'un salarié
    }
    
    public function ficheentrepriseAction()
    {
    	$this->view->title = "Fiche entreprise"; // Titre de la page
    	
    	// Fiche en formulaire (bloqué) d'une entreprise
    }
    
    public function listeAction()
    {
    	$this->view->title = "Les entreprises"; // Titre de la page
    	
    	// Liste des entreprises pour les enseignants tuteurs
    }
    
}







