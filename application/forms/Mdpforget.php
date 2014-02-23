<?php

class Application_Form_Mdpforget extends Zend_Form
{

   	public function init()
	{
		$this->setName('formMdpForget')
			 ->setAttrib('id','formMdpForget');
		
		//Ajoute des élèments au formulaire
		//Instancie un nouveau champ de type text
		$inputMdpForgetConnexion = new Zend_Form_Element_Text('inputMail');
		$inputMdpForgetConnexion->setAttrib('id','inputMdp')
					   			->setLabel('Adresse e-mail :')
					   			->setRequired(true)
					  			->addValidator('NotEmpty')
								->addValidator(new Zend_Validate_Db_RecordExists(array('table'=>'membre','field'=>'mail')))
					  			->addFilter('StripTags')
				  	   			->addFilter('StringTrim');
		//Instancie un nouveau champ de type submit
		$submitMdpForget = new Zend_Form_Element_Submit('submitValider');	
		$submitMdpForget->setAttrib('id','submitMdp')
						->setName('Valider');
							
		// Ajoute tout les éléments dans le formulaire	
		$this->addElements(array($inputMdpForgetConnexion,$submitMdpForget));
	}

}

