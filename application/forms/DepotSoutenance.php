<?php

class Application_Form_DepotSoutenance extends Zend_Form
{
	private $regexDate = '`^(0[1-9]|[12][0-9]|3[01])[/.](0[1-9]|1[012])[/.](19|20)\d\d$`';
	
   	public function __construct($idStageUpdate = null)
    {
  		parent::__construct($idStageUpdate);
  		
		$this->setName('formDepot');
		
		if($idStageUpdate != null) {
			// Recupere les tuteurs de la bdd
			$modelStage = new Application_Model_DbTable_Stage();
			$unStage = $modelStage->getInfoStage((int)$idStageUpdate);
			$stage = new Zend_Form_Element_Select('idStage');
			$stage	->setLabel('Stage')
					->setAttrib('class','form-control')
					->addMultiOption(null, $unStage[0]->libelleStage);
		} else {
			// Recupere les tuteurs de la bdd
			$modelStage = new Application_Model_DbTable_Stage();
			$lesStagesForSoutenance = $modelStage->getStageForSoutenance();
			$stage = new Zend_Form_Element_Select('idStage');
			$stage	->setLabel('Stage')
					->setAttrib('class','form-control')
					->setRequired(true)
					->addValidator('NotEmpty')
					->addFilter('StripTags')
					->addFilter('StringTrim');
			foreach ($lesStagesForSoutenance as $unStage){
				if($unStage->idSoutenance == null) $stage->addMultiOption($unStage->idStage, $unStage->libelleStage);
			}
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
		$this->addElements(array($stage, $dateSoutenance, $salleSoutenance, $submitSoutenance));
	}

}

