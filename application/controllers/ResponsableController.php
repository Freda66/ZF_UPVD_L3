<?php

class ResponsableController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
	
	public function indexAction()
	{
		/* OBJET MEMBRE */
		// Crée un objet dbTable membre
		$membre = new Application_Model_DbTable_Membre();
		// Envoie a la vue l'objet membre connecter
		$membreAuth = $membre->getMembreAuth();
		$this->view->membre = $membreAuth;
		
		/* FORMULAIRE INFORMATION */
    	// Instanciation du formulaire
    	$formInformation = new Application_Form_Information();
		// Traduit les messages d'erreur
		$formInformation->setTranslator(Bootstrap::_initTranslate());
    	// Envoie a la vue le formulaire
    	$this->view->formInformation = $formInformation;
		// Si le formulaire a été posté
		if($this->getRequest()->isPost())
		{
			// Recupere les informations du formulaire
			$formData = $this->getRequest()->getPost();

			// Si les informations sont valides par rapport au formulaire init (initiale)
			if($formInformation->isValid($formData))
			{
				// Recupere les attributs dans des variables
				$idAdresse = $formInformation->getValue('idAdresse');
				$nom = $formInformation->getValue('nom');
				$prenom = $formInformation->getValue('prenom');
				$civilite = $formInformation->getValue('civilite');
				$tel = $formInformation->getValue('tel');
				$adresse = $formInformation->getValue('adresse');
				$cp = $formInformation->getValue('codepostal');
				$ville = $formInformation->getValue('ville');
				$idPays = $formInformation->getValue('pays');
				
				$result = $membre->updateMembre($idAdresse, $nom, $prenom, $civilite, $tel, $adresse, $cp, $ville, $idPays);
				if($result)
				{
					$this->_redirect('/membre/membre');
				}
			}
			// Si le formulaire n'est pas valide on le remplie a nouveau
			else 
			{
				$formInformation->populate($formData);
			}
		}
	
		/* FORMULAIRE MODIFIER MDP */
		// Instanciation du formulaire
    	$formMdpUpdate = new Application_Form_MdpUpdate();
    	// Envoie a la vue le formulaire
    	$this->view->formMdpUpdate = $formMdpUpdate;
	}

	public function mdpupdateAction()
	{
		$membre = new Application_Model_DbTable_Membre();
		// Envoie a la vue l'objet membre connecter
		$membreAuth = $membre->getMembreAuth();
		/* FORMULAIRE MODIFIER MDP */
    	$formMdpUpdate = new Application_Form_MdpUpdate();
		// Si le formulaire a été posté
		if($this->getRequest()->isPost())
		{
			// Recupere les informations du formulaire
			$formData = $this->getRequest()->getPost();

			// Si les informations sont valides par rapport au formulaire init (initiale)
			if($formMdpUpdate->isValid($formData))
			{
				// Recupere les attributs dans des variables
				$mdpActuel = $formData['mdpActuel'];
				$mdpNew = $formData['mdpNew'];
				$confirmMdpNew = $formData['confirmMdpNew'];	
				
				// Vérifie que les deux mdp sont identique
				if($mdpNew == $confirmMdpNew)
				{
					// Verifie qu'il existe un membre avec cette id et ce mdp actuel
					$verifMdp = $membre->verifMdp($membreAuth->id, $mdpActuel);
					// Si oui, on modifie le mot de passe du membre
					if($verifMdp)
					{
						$result = $membre->updateMdpMembre($membreAuth->id, $mdpNew);
					}
					else 
					{
						$result = false;						
					}
				}
				else 
				{
					$this->view->message = "Les deux mot de passe sont différent";
				}
				// Si la modification a été effectué, on redirige le membre vers son compte
				if($result)
				{
					$this->_redirect('/membre/membre');
				}
				else 
				{
					$this->view->message = "Erreur dans la saisie du login/mot de passe";					
				}
			}
			else 
			{
				$this->view->message = "Formulaire invalide";	
			}
		}
		else 
		{
			$this->view->message = "Aucun post du formulaire";
		}
	}


	
	public function loginAction()
    {
    	// Instancie un formulaire de connexion
        $login = new Application_Form_Login();

		// Si le formulaire a été envoyé
        if ($this->_request->isPost()) 
        {
        	// On vérifie les données
  			$formData = $this->_request->getPost();
			// Si les données sont valides
  			if ($login->isValid($formData)) 
  			{
				// On affecte les valeurs rentrées aux variables
				$email = $formData['identifiant'];
				$mdp = $formData['mdp'];
				// Crée un objet Authentification
				$authentification = new Application_Model_DbTable_Membre();
				// Appel la fonction afin de connecter le membre
				$result = $authentification->authentificationMembre($email, $mdp);
				// Si la connexion a réussi
				if($result)
				{
					// Redirection
					$this->_redirect('/index/index');
				}
				// Si result == false
				else 
					$this->view->message = "Echec de la connexion (compte désactivé ou erreur login/mot de passe)";
    		}
			// Si le formulaire poster est invalide
			else
				$this->view->message = "Tous les champs doivent être remplie";
		}
		// Si il y a un echec de l'envoie du formulaire
		else
			$this->view->message = "Echec de l'envoie du formulaire";
    }

    public function logoutAction()
    {
    	// Efface les sessions en cour
        Zend_Auth::getInstance()->clearIdentity();
		
		// Redirection
		$this->_redirect('/index/');
    }   

}
