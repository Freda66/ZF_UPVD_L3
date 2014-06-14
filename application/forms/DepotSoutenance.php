<?php

class Application_Form_DepotSoutenance extends Zend_Form
{
	private $regexDate = '`^(0[1-9]|[12][0-9]|3[01])[/.](0[1-9]|1[012])[/.](19|20)\d\d$`';
	
   	public function init()
	{
		$this->setName('formDepot');
		
		// Recupere les tuteurs de la bdd
		$session = Zend_Auth::getInstance()->getStorage()->read();
		$modelStage = new Application_Model_DbTable_Stage();
		$lesStagesSansSoutenance = $modelStage->getStageWithoutSoutenance();
		// Instancie un nouveau champ de type select
		$idStage = new Zend_Form_Element_Select('idStage');
		$idStage->setLabel('Stage')
					->setAttrib('class','form-control')
					->setRequired(true)
					->addValidator('NotEmpty')
					->addFilter('StripTags')
					->addFilter('StringTrim');
		// Parcour et ajout dans le selecteur
		foreach($lesStagesSansSoutenance as $unStage){
			var_dump($unStage->libelleStage);
			$idStage->addMultiOption($unStage->libelleStage);
		}
		
		// Instancie un nouveau champ de type date
		$dateSoutenance = new Zend_Form_Element_Text('dateSoutenance');
		$dateSoutenance	->setLabel('Date (AAAA/MM/JJ HH:mm)')
						->setAttrib('class','form-control')
						->setRequired(true)
						->addValidator('NotEmpty')
						->addFilter('StripTags')
						->addFilter('StringTrim');
		
		//Instancie un nouveau champ de type text
		$salleSoutenance = new Zend_Form_Element_Text('salleSoutenance');
		$salleSoutenance	->setLabel('Salle')
							->setAttrib('class','form-control')
							->setRequired(true)
							->addValidator('NotEmpty')
							->addFilter('StripTags')
							->addFilter('StringTrim');
		
	   	// Bouton submit
		$submitSoutenance = new Zend_Form_Element_Submit('submitDepotSoutenance');	
		$submitSoutenance	->setAttrib('class','btn btn-primary form-control')
							->setAttrib('id','submitDepotStage')
							->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($idStage, $dateSoutenance, $salleSoutenance, $submitSoutenance));
	}

}

