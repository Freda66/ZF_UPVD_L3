<?php

class Application_Form_Personne extends Zend_Form
{
   	public function __construct($options = null)
    {
  		parent::__construct($options);
  		
		$this->setName('formResponsableDepot');
		
		//Instancie un nouveau champ de type text
		$nomPersonne = new Zend_Form_Element_Text('nomPersonne');
		$nomPersonne	->setLabel('Nom')
					   	->setAttrib('class','form-control')
					   	->setRequired(true)
					   	->addValidator('NotEmpty')
					   	->addFilter('StripTags')
				  	   	->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$prenomPersonne = new Zend_Form_Element_Text('prenomPersonne');
		$prenomPersonne	->setLabel('Prénom')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$fonctionPersonne = new Zend_Form_Element_Text('fonctionPersonne');
		$fonctionPersonne	->setLabel('Fonction')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$telPortPersonne = new Zend_Form_Element_Text('telPortPersonne');
		$telPortPersonne	->setLabel('Téléphone portable')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$telPostePersonne = new Zend_Form_Element_Text('telPostePersonne');
		$telPostePersonne	->setLabel('Téléphone (poste)')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');

		//Instancie un nouveau champ de type text
		$emailPersonne = new Zend_Form_Element_Text('emailPersonne');
		$emailPersonne	->setLabel('E-mail')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');
							
		// Bouton submit
		$submitPersonne = new Zend_Form_Element_Submit('submitEntreprise');	
		$submitPersonne	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($nomPersonne, $prenomPersonne, $prenomPersonne, $fonctionPersonne, $telPortPersonne, $telPostePersonne, $emailPersonne, $submitPersonne));
	}

}

