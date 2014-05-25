<?php

class Application_Form_DepotStage extends Zend_Form
{
	private $regexDate = '`^(0[1-9]|[12][0-9]|3[01])[/.](0[1-9]|1[012])[/.](19|20)\d\d$`';
	
   	public function init()
	{
		$this->setName('formDepot');
		
		// Ajoute des elements au formulaire
		
		//Instancie un nouveau champ de type text
		$libelleStage = new Zend_Form_Element_Text('libelleStage');
		$libelleStage->setLabel('Libelle')
					   ->setAttrib('class','form-control')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type date
		$dateDebutStage = new Zend_Form_Element_Text('dateDebutStage');
		$dateDebutStage->setLabel('Date de debut (AAAA/MM/JJ)')
					   ->setAttrib('class','form-control')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type date
		$dateFinStage = new Zend_Form_Element_Text('dateFinStage');
		$dateFinStage	->setLabel('Date de fin (AAAA/MM/JJ)')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		// Recupere les tuteurs de la bdd
    	$session = Zend_Auth::getInstance()->getStorage()->read();
		$modelPersonne = new Application_Model_DbTable_Personne();
		$lesPersonnes = $modelPersonne->getPersonneByEntreprise($session->infoUser->identifiant);
		// Instancie un nouveau champ de type select
		$tuteurStage = new Zend_Form_Element_Select('idTuteur');
		$tuteurStage->setLabel('Tuteur')
					   ->setAttrib('class','form-control')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
					   // Parcour et ajout dans le selecteur
					   foreach($lesPersonnes as $unTuteur){
					   		$tuteurStage->addMultiOption($unTuteur->idPersonne, $unTuteur->nomPersonne.' '.$unTuteur->prenomPersonne);
					   }

	   // Instancie un nouveau champ de type text
	   $descriptionStage = new Zend_Form_Element_Textarea('descriptionStage');
	   $descriptionStage->setLabel('Description')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim')
					    ->setAttrib('COLS', '40')
					    ->setAttrib('ROWS', '10');;

	   	// Bouton submit
		$submitStage = new Zend_Form_Element_Submit('submitDepotStage');	
		$submitStage	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($libelleStage, $dateDebutStage, $dateFinStage, $tuteurStage, $descriptionStage, $clearStage, $submitStage));
	}

}

