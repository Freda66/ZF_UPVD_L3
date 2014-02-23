<?php

class Application_Form_Login extends Zend_Form
{

   public function init()
	{
		$this->setName('login')
			 ->setAttrib('id','login-form-top');
		
		//Ajoute des élèments au formulaire
		//Instancie un nouveau champ de type text
		$loginConnexion = new Zend_Form_Element_Text('loginconnexion');
		$loginConnexion->setLabel('E-mail');
		$loginConnexion->setAttrib('id','login-connexion')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		//Instancie un nouveau champ de type password	
		$pwConnexion = new Zend_Form_Element_Password('pwconnexion');	
		$pwConnexion->setAttrib('id','pw-connexion');	
		$pwConnexion->setLabel('Mot de passe')	
					->setRequired(true)	
					->addValidator('NotEmpty')	
					->addFilter('StripTags')	
					->addFilter('StringTrim');	
		//Instancie un nouveau champ de type submit	
		$submitConnexion = new Zend_Form_Element_Submit('Connexion');	
		$submitConnexion->setAttrib('id','submitconnexion');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($loginConnexion,$pwConnexion,$submitConnexion));
	}

}

