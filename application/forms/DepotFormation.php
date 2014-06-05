<?php

class Application_Form_DepotFormation extends Zend_Form
{
   	public function init()
	{
		$this->setName('formDepot');
		
		// Recupere les enseignant de la bdd
		$modelEnseignant = new Application_Model_DbTable_Enseignant();
		$lesEnseignants = $modelEnseignant->fetchAll();
		// Instancie un nouveau champ de type select
		$idEnseignantResponsableFormation = new Zend_Form_Element_Select('idEnseignantResponsable');
		$idEnseignantResponsableFormation->setLabel('Enseignant responsable')
					   ->setAttrib('class','form-control')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
					   // Parcour et ajout dans le selecteur
					   foreach($lesEnseignants as $unEnseignant){
					   		$idEnseignantResponsableFormation->addMultiOption($unEnseignant->idEnseignant, $unEnseignant->nomEnseignant.' '.$unEnseignant->prenomEnseignant);
					   }

		//Instancie un nouveau champ de type text
		$libelleFormation = new Zend_Form_Element_Text('libelleFormation');
		$libelleFormation	->setLabel('Libelle')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$niveauFormation = new Zend_Form_Element_Select('niveauFormation');
		$niveauFormation	->setLabel('Niveau')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim')
							->addMultiOptions(array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'));
		
		//Instancie un nouveau champ de type text
		$specialiteFormation = new Zend_Form_Element_Text('specialiteFormation');
		$specialiteFormation	->setLabel('Spécialité')
								->setAttrib('class','form-control')
								->setRequired(true)
								->addValidator('NotEmpty')
								->addFilter('StripTags')
								->addFilter('StringTrim');
							   
	  

	   	// Bouton submit
		$submitFormation = new Zend_Form_Element_Submit('submitDepotFormation');	
		$submitFormation	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($idEnseignantResponsableFormation, $libelleFormation, $niveauFormation, $specialiteFormation, $submitFormation));
	}

}

