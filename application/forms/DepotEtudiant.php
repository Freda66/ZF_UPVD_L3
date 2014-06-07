<?php

class Application_Form_DepotEtudiant extends Zend_Form
{
   	public function __construct($isUpdate = false)
    {
  		parent::__construct($isUpdate);
  		
		$this->setName('formResponsableDepot');
		
		//Instancie un nouveau champ de type text
		if($isUpdate == false) { 
			$ineEtudiant = new Zend_Form_Element_Text('ineEtudiant'); 
			$ineEtudiant	->setLabel('INE')
						   	->setAttrib('class','form-control')
						   	->setRequired(true)
						   	->addValidator('NotEmpty')
						   	->addFilter('StripTags')
					  	   	->addFilter('StringTrim');
		} else $ineEtudiant = new Zend_Form_Element_Hidden('ineEtudiant');
		
		// Recupere les formations de la bdd
		$modelFormation = new Application_Model_DbTable_Formation();
		$lesFormations = $modelFormation->fetchAll();
		// Instancie un nouveau champ de type select
		$idFormation = new Zend_Form_Element_Select('idFormation');
		$idFormation	->setLabel('Formation')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		// Parcour et ajout dans le check
		foreach($lesFormations as $uneFormation){
			$idFormation->addMultiOption($uneFormation->codeFormation, $uneFormation->libelleFormation.' '.$uneFormation->niveauFormation.' '.$uneFormation->specialiteFormation);
		}
		
		$nomEtudiant= new Zend_Form_Element_Text('nomEtudiant');
		$nomEtudiant	->setLabel('Nom')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');
		
		$prenomEtudiant= new Zend_Form_Element_Text('prenomEtudiant');
		$prenomEtudiant	->setLabel('Prénom')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');

		
		//Instancie un nouveau champ de type text
		$loginEtudiant = new Zend_Form_Element_Text('loginEtudiant');
		$loginEtudiant->setLabel('Login')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type password
		$mdpEtudiant = new Zend_Form_Element_Password('mdpEtudiant');
		$mdpEtudiant	->setLabel('Password')
						->setAttrib('class','form-control')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		// Modifie l'attribut setRequired si mode Modification
		if($isUpdate == false){
			$mdpEtudiant	->setRequired(true)
							->addValidator('NotEmpty');
		}
		
		// E-Mail
		$emailEtudiant= new Zend_Form_Element_Text('emailEtudiant');
		$emailEtudiant	->setLabel('E-mail')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
	
		// Bouton submit
		$submitEtudiant = new Zend_Form_Element_Submit('submitEnseignant');	
		$submitEtudiant	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($ineEtudiant, $idFormation, $nomEtudiant, $prenomEtudiant, $loginEtudiant, $mdpEtudiant, $emailEtudiant, $submitEtudiant));
	}

}

