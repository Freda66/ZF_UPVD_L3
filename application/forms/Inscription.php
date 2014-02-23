<?php

class Application_Form_Inscription extends Zend_Form
{

   	public function init()
	{
		$this->setName('formInscription')
			 ->setAttrib('id','inscription');
		
		// Ajoute des élèments au formulaire
		
		//Instancie un nouveau champ de type text
		$identifiantInscription = new Zend_Form_Element_Text('identifiant');
		$identifiantInscription->setLabel('Identifiant')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'membre','field'=>'pseudo')))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type password	
		$mdpInscription = new Zend_Form_Element_Password('mdp');	
		$mdpInscription->setAttrib('class','inputInscription')
					 ->setLabel('Mot de passe')	
					 ->setRequired(true)	
					 ->addValidator('NotEmpty')	
					 ->addFilter('StripTags')	
					 ->addFilter('StringTrim');	
	 	// Instancie un nouveau champ de type password	
		$mdpConfirmeInscription = new Zend_Form_Element_Password('mdpConfirm');	
		$mdpConfirmeInscription->setAttrib('class','inputInscription')
					 ->setLabel('Confirmer mot de passe')	
					 ->setRequired(true)	
					 ->addValidator(new Zend_Validate_Identical('mdp'))	
					 ->addFilter('StripTags')	
					 ->addFilter('StringTrim');	
		// Instancie un nouveau champ de type text
		$emailInscription = new Zend_Form_Element_Text('mail');
		$emailInscription->setLabel('E-mail')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator(new Zend_Validate_EmailAddress())
					   ->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'membre','field'=>'mail')))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$nomInscription = new Zend_Form_Element_Text('nom');
		$nomInscription->setLabel('Nom')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$prenomInscription = new Zend_Form_Element_Text('prenom');
		$prenomInscription->setLabel('Prénom')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$datenaissanceInscription = new Zend_Form_Element_Text('dateNaissance');
		$datenaissanceInscription->setLabel('Date de naissance (JJ/MM/AAAA)')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$civiliteInscription = new Zend_Form_Element_Select('civilite');
		$civiliteInscription->setLabel('Civilité')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->addMultiOptions(array(
					   			'Monsieur'=>'M.',
								'Madame'=>'Mme.'));
		// Instancie un nouveau champ de type text
		$telInscription = new Zend_Form_Element_Text('tel');
		$telInscription->setLabel('Téléphone')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator('StringLength', false, array('min'=>10,'max'=>10))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$adresseInscription = new Zend_Form_Element_Text('adresse');
		$adresseInscription->setLabel('Adresse')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$cpInscription = new Zend_Form_Element_Text('codepostal');
		$cpInscription->setLabel('Code Postal')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator('StringLength', false, array('min'=>5,'max'=>5))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$villeInscription = new Zend_Form_Element_Text('ville');
		$villeInscription->setLabel('Ville')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		// Recupere les pays de la bdd
		$pays = new Application_Model_DbTable_Pays();
		$pays = $pays->getPays();
		// Crée le champ
		$paysInscription = new Zend_Form_Element_Select('pays');
		$paysInscription->setLabel('Pays')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
					   // Parcour et ajout dans le selecteur 
					   foreach($pays as $unPays):
					   		$paysInscription->addMultiOption($unPays->id_pays, $unPays->en);
					   endforeach;
		$paysInscription->setValue(67); // Par defaut on selectionne le pays "France"
		// Instancie un nouveau champ de type submit
		$submitInscription = new Zend_Form_Element_Submit('submitInscription');	
		$submitInscription->setAttrib('id','submitInscription')
						->setName('Inscription');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($identifiantInscription,$mdpInscription,$mdpConfirmeInscription,$emailInscription,$nomInscription,$prenomInscription,$datenaissanceInscription,$civiliteInscription,$telInscription,$adresseInscription,$cpInscription,$villeInscription,$paysInscription,$submitInscription));
	}

}

