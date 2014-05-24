<?php

class Application_Form_Enseignant extends Zend_Form
{
   	public function __construct($options = null)
    {
  		parent::__construct($options);
  		
		$this->setName('formResponsableDepot');
		
		//Instancie un nouveau champ de type text
		$nomEnseignant = new Zend_Form_Element_Text('nomEnseignant');
		$nomEnseignant	->setLabel('Nom')
					   	->setAttrib('class','form-control')
					   	->setRequired(true)
					   	->addValidator('NotEmpty')
					   	->addFilter('StripTags')
				  	   	->addFilter('StringTrim');
		
		$prenomEnseignant = new Zend_Form_Element_Text('prenomEnseignant');
		$prenomEnseignant	->setLabel('Prenom')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');

		// Instancie un nouveau champ de type select
		$fonctionEnseignant = new Zend_Form_Element_Select('fonctionEnseignant');
		$fonctionEnseignant->setLabel('Fonction')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addMultiOptions(array("Enseignant"=>"Enseignant", "Intervenant"=>"Intervenant","Administration"=>"Administration"));

		// Instancie un nouveau champ de type select
		$specialiteEnseignant = new Zend_Form_Element_Select('specialiteEnseignant');
		$specialiteEnseignant->setLabel('Specialite')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addMultiOptions(array("Informatique"=>"Informatique", "Robotique"=>"Robotique","Management"=>"Management"));

		
		//Instancie un nouveau champ de type text
		$loginEnseignant = new Zend_Form_Element_Text('loginEnseignant');
		$loginEnseignant->setLabel('Login')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type password
		$mdpEnseignant = new Zend_Form_Element_Password('mdpEnseignant');
		$mdpEnseignant	->setLabel('Password')
						->setAttrib('class','form-control')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		// Modifie l'attribut setRequired si mode Modification
		if($options == null){
			$mdpEnseignant	->setRequired(true)
							->addValidator('NotEmpty');
		}
		
		// Instancie un nouveau champ de type select
		$isResponsableSiteEnseignant = new Zend_Form_Element_Select('isResponsableSiteEnseignant');
		$isResponsableSiteEnseignant->setLabel('Responsable du site')
		->setAttrib('class','form-control')
		->setRequired(true)
		->addValidator('NotEmpty')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addMultiOptions(array("0"=>"Non", "1"=>"Oui"));
		
	
	   // Recupere les formations de la bdd
	   $modelFormation = new Application_Model_DbTable_Formation();
	   $lesFormations = $modelFormation->fetchAll();
	   // Instancie un nouveau champ de type select
	   $idFormation = new Zend_Form_Element_MultiCheckbox('idFormation');
	   $idFormation	->setLabel('Formation')
	   				->setSeparator('');
	   // Parcour et ajout dans le check
	   foreach($lesFormations as $uneFormation){
	   		$idFormation->addMultiOption($uneFormation->codeFormation, $uneFormation->libelleFormation.' '.$uneFormation->niveauFormation.' '.$uneFormation->specialiteFormation);
	   }
	   // Si il y a des formations pour un enseignant
	   if($options != null){
		   	// Déclare un tableau
	   		$tabLesFormationsCheck = array(); 
	   		// Met les formations dans le tableau
	   		foreach($options as $uneOption) array_push($tabLesFormationsCheck, $uneOption->idFormation);  
	   		// Coche les formations
	   		$idFormation->setValue($tabLesFormationsCheck);
	   }
	   
		// Bouton submit
		$submitEnseignant = new Zend_Form_Element_Submit('submitEnseignant');	
		$submitEnseignant	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($nomEnseignant, $prenomEnseignant, $fonctionEnseignant, $specialiteEnseignant, $loginEnseignant, $mdpEnseignant, $isResponsableSiteEnseignant, $idFormation, $submitEnseignant));
	}

}

