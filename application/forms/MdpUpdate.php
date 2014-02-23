<?php

class Application_Form_MdpUpdate extends Zend_Form
{

   	public function init()
	{
		
		$this->setName('formMdpUpdate')
			 ->setAttrib('id','mdpUpdate');
		
		// Ajoute des élèments au formulaire
		// Instancie un nouveau champ de type text
		$mdpActuel = new Zend_Form_Element_Password('mdpActuel');
		$mdpActuel->setLabel('Mot de passe actuel')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$mdpNew = new Zend_Form_Element_Password('mdpNew');
		$mdpNew->setLabel('Nouveau mot de passe')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');
		// Instancie un nouveau champ de type text
		$confirmMdp = new Zend_Form_Element_Password('confirmMdpNew');
		$confirmMdp->setLabel('Confirmer mot de passe')
					   ->setAttrib('class','inputInscription')
					   ->setRequired(true)
					   ->addValidator('NotEmpty')
					   ->addValidator(new Zend_Validate_Identical('mdpNew'))	
					   ->addFilter('StripTags')
				  	   ->addFilter('StringTrim');			   
		// Instancie un nouveau champ de type submit
		$submitMdpUpdate = new Zend_Form_Element_Submit('submitMdpUpdate');	
		$submitMdpUpdate->setAttrib('id','submitMdpUpdate')
						  ->setName('Modifier');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($mdpActuel,$mdpNew,$confirmMdp,$submitMdpUpdate));
	}

}

