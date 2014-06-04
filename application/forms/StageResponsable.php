<?php

class Application_Form_StageResponsable extends Zend_Form
{
   	public function __construct($options = null)
    {
  		parent::__construct($options);
  		
		$this->setName('formDepot');
		
		// Ajoute des elements au formulaire
		// Recupere les etudiants de la bdd
		$modelEtudiant = new Application_Model_DbTable_Etudiant();
		$lesEtudiants = $modelEtudiant->getListeEtudiant();
		// Instancie un nouveau champ de type select
		$etudiantStage = new Zend_Form_Element_Select('idEtudiant');
		$etudiantStage->setLabel('Étudiant')
					->setAttrib('class','form-control')
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addMultiOption('', '');
					// Parcour et ajout dans le selecteur
					foreach($lesEtudiants as $unEtudiant){
						$etudiantStage->addMultiOption($unEtudiant->ineEtudiant, $unEtudiant->nomEtudiant.' '.$unEtudiant->prenomEtudiant);
					}
		
		// Recupere les enseignants de la bdd
		$modelEnseignant = new Application_Model_DbTable_Enseignant();
		$lesEnseignants = $modelEnseignant->getListeEnseignant();
		// Instancie un nouveau champ de type select
		$tuteurStage = new Zend_Form_Element_Select('idEnseignantTuteur');
		$tuteurStage->setLabel('Tuteur enseignant')
					   ->setAttrib('class','form-control')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
				  	   ->addMultiOption('', '');
					   // Parcour et ajout dans le selecteur
					   foreach($lesEnseignants as $unEnseignant){
					   		$tuteurStage->addMultiOption($unEnseignant->idEnseignant, $unEnseignant->nomEnseignant.' '.$unEnseignant->prenomEnseignant);
					   }

		// Recupere les enseignants de la bdd
		$etatStage = new Zend_Form_Element_Select('etatStage');
		$etatStage->setLabel('État')
					   ->setAttrib('class','form-control')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
				  	   ->addMultiOptions(array(1=>'Activé', 0=>'En attente de validation', -1=>'Désactivé'));

		// Recupere les formations de la bdd
		$modelFormation = new Application_Model_DbTable_Formation();
		$lesFormations = $modelFormation->fetchAll();
		// Instancie un nouveau champ de type select
		$idFormation = new Zend_Form_Element_MultiCheckbox('lesFormations');
		$idFormation	->setLabel('Formation')
						->setSeparator(' &nbsp; ');
		// Parcour et ajout dans le check
		foreach($lesFormations as $uneFormation){
			$idFormation->addMultiOption($uneFormation->codeFormation, $uneFormation->libelleFormation.' '.$uneFormation->niveauFormation.' '.$uneFormation->specialiteFormation);
		}
		// Si il y a des formations pour un stage
		if($options != null){
			// Déclare un tableau
			$tabLesFormationsCheck = array();
			// Met les formations dans le tableau
			foreach($options as $uneOption) array_push($tabLesFormationsCheck, $uneOption->idFormation);
			// Coche les formations
			$idFormation->setValue($tabLesFormationsCheck);
		}
		
	   	// Bouton submit
		$submitStage = new Zend_Form_Element_Submit('submitDepotStage');	
		$submitStage->setAttrib('class','btn btn-primary form-control')
					->setAttrib('id','submitDepotStage')
					->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($etudiantStage, $tuteurStage ,$etatStage, $idFormation, $submitStage));
	}

}

