<?php

class Application_Form_Information extends Zend_Form
{

   	public function init()
	{
		// Crée un objet dbTable membre
		$membre = new Application_Model_DbTable_Membre();
		// Envoie a la vue l'objet membre connecter
		$membre = $membre->getInformationMembre();
		$membre = $membre[0];
		
		$this->setName('formInformation')
			 ->setAttrib('id','information')
 			 ->setMethod('post');
		
		// Ajoute des élèments au formulaire
		// Instancie un nouveau champ de type hidden
		$idAdresseInformation = new Zend_Form_Element_Hidden('idAdresse');
		$idAdresseInformation->setValue($membre->id_adr_habiter);
		//Instancie un nouveau champ de type text
		$identifiantInformation = new Zend_Form_Element_Text('pseudo');
		$identifiantInformation->setLabel('Identifiant')
					   ->setAttrib('class','inputInscription')
					   ->setAttrib('disabled', 'disabled')
					   ->setValue($membre->pseudo);
		// Instancie un nouveau champ de type text
		$emailInformation = new Zend_Form_Element_Text('mail');
		$emailInformation->setLabel('E-mail')
					   ->setAttrib('class','inputInscription')
					   ->setAttrib('disabled', 'disabled')
					   ->setValue($membre->mail);
		// Instancie un nouveau champ de type text
		$nomInformation = new Zend_Form_Element_Text('nom');
		$nomInformation->setLabel('Nom')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->nom);
		// Instancie un nouveau champ de type text
		$prenomInformation = new Zend_Form_Element_Text('prenom');
		$prenomInformation->setLabel('Prénom')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->prenom);
		// Instancie un nouveau champ de type text
		$datenaissanceInformation = new Zend_Form_Element_Text('datenaissance');
		$datenaissanceInformation->setLabel('Date de naissance (JJ/MM/AAAA)')
					   ->setAttrib('class','inputInscription')
					   ->setAttrib('disabled','disabled')
					   ->setValue($membre->datenaissance);
		// Instancie un nouveau champ de type text
		$civiliteInformation = new Zend_Form_Element_Select('civilite');
		$civiliteInformation->setLabel('Civilité')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->addMultiOptions(array(
					   			'Monsieur'=>'M.',
								'Madame'=>'Mme.'))
					   ->setValue($membre->civilite);
		// Instancie un nouveau champ de type text
		$telInformation = new Zend_Form_Element_Text('tel');
		$telInformation->setLabel('Téléphone')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator('StringLength', false, array('min'=>10,'max'=>10))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->tel);
		// Instancie un nouveau champ de type text
		$adresseInformation = new Zend_Form_Element_Text('adresse');
		$adresseInformation->setLabel('Adresse')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->adresse);
		// Instancie un nouveau champ de type text
		$cpInformation = new Zend_Form_Element_Text('codepostal');
		$cpInformation->setLabel('Code Postal')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator('StringLength', false, array('min'=>5,'max'=>5))
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->codepostal);
		// Instancie un nouveau champ de type text
		$villeInformation = new Zend_Form_Element_Text('ville');
		$villeInformation->setLabel('Ville')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim')
					   ->setValue($membre->ville);
		// Instancie un nouveau champ de type text
		// Recupere les pays de la bdd
		$pays = new Application_Model_DbTable_Pays();
		$pays = $pays->getPays();
		// Crée le champ
		$paysInformation = new Zend_Form_Element_Select('pays');
		$paysInformation->setLabel('Pays')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
					   // Parcour et ajout dans le selecteur 
					   foreach($pays as $unPays):
					   		$paysInformation->addMultiOption($unPays->id_pays, $unPays->en);
					   endforeach;
		$paysInformation->setValue($membre->id_pays);			   
		// Instancie un nouveau champ de type submit
		$submitInformation = new Zend_Form_Element_Submit('submitInformation');	
		$submitInformation->setAttrib('id','submitInformation')
						  ->setName('Enregistrer');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($idAdresseInformation,$identifiantInformation,$emailInformation,$nomInformation,$prenomInformation,$datenaissanceInformation,$civiliteInformation,$telInformation,$adresseInformation,$cpInformation,$villeInformation,$paysInformation,$submitInformation));
	}

}

