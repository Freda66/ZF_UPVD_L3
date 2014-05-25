<?php

class Application_Form_DepotEntreprise extends Zend_Form
{
   	public function __construct($lesEmployes = null)
    {
  		parent::__construct($lesEmployes);
  		
		$this->setName('formResponsableDepot');
		
		//Instancie un nouveau champ de type text
		$rsEntreprise = new Zend_Form_Element_Text('rsEntreprise');
		$rsEntreprise	->setLabel('Raison social')
					   	->setAttrib('class','form-control')
					   	->setRequired(true)
					   	->addValidator('NotEmpty')
					   	->addFilter('StripTags')
				  	   	->addFilter('StringTrim');
		
		$siretEntreprise = new Zend_Form_Element_Text('siretEntreprise');
		$siretEntreprise	->setLabel('Siret')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');

		// Instancie un nouveau champ de type select
		$dirigeantEntreprise = new Zend_Form_Element_Select('idPersonneDirigeant');
		$dirigeantEntreprise->setLabel('Dirigeant')
		->setAttrib('class','form-control')
		->addFilter('StripTags')
		->addFilter('StringTrim');
		foreach ($lesEmployes as $unePersonne){
			$dirigeantEntreprise->addMultiOption($unePersonne->idPersonne,$unePersonne->nomPersonne." ".$unePersonne->prenomPersonne);
		}
		
		//Instancie un nouveau champ de type text
		$siretEntreprise = new Zend_Form_Element_Text('siretEntreprise');
		$siretEntreprise	->setLabel('Siret')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$adrRueEntreprise = new Zend_Form_Element_Text('adrRueEntreprise');
		$adrRueEntreprise	->setLabel('Rue')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$adrCpEntreprise = new Zend_Form_Element_Text('adrCpEntreprise');
		$adrCpEntreprise	->setLabel('Code Postal')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$adrVilleEntreprise = new Zend_Form_Element_Text('adrVilleEntreprise');
		$adrVilleEntreprise	->setLabel('Ville')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$telEntreprise = new Zend_Form_Element_Text('telEntreprise');
		$telEntreprise	->setLabel('Téléphone')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$emailEntreprise = new Zend_Form_Element_Text('emailEntreprise');
		$emailEntreprise	->setLabel('E-mail')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$loginEntreprise = new Zend_Form_Element_Text('loginEntreprise');
		$loginEntreprise->setLabel('Login')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type password
		$mdpEntreprise = new Zend_Form_Element_Password('mdpEntreprise');
		$mdpEntreprise	->setLabel('Password')
						->setAttrib('class','form-control')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		// Modifie l'attribut setRequired si mode Modification
		if($lesEmployes == null){
			$mdpEntreprise	->setRequired(true)
							->addValidator('NotEmpty');
		}
		
		// Bouton submit
		$submitEntreprise = new Zend_Form_Element_Submit('submitEntreprise');	
		$submitEntreprise	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($rsEntreprise, $dirigeantEntreprise, $siretEntreprise, $adrRueEntreprise, $adrCpEntreprise, $adrVilleEntreprise, $telEntreprise, $emailEntreprise, $loginEntreprise, $mdpEntreprise, $submitEntreprise));
	}

}

