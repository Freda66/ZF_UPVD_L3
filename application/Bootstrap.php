<?php
// Peut être utiliser pour executer tout code spécifique au lancement
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Initialise l'auto chargement des ressources
	 * @return Zend_Loader_Autoloader_Resource
	 */
	protected function _initAutoloadRessource()
	{
		// Configuration de l'Autoload
		$ressourceLoader = new Zend_Loader_Autoloader_Resource(array(
			'namespace' => 'Default',
			'basePath'  => dirname(__FILE__),
		));
	 
		// Permet d'indiquer les répertoires dans lesquels se trouveront nos classes: l'ACL et le pugin
		$ressourceLoader->addResourceType('form', 'forms/', 'Form')
						->addResourceType('acl', 'acls/', 'Acl')
						->addResourceType('model', 'models/', 'Model')
						->addResourceType('plugin', 'plugins/', 'Controller_Plugin')
						// Ajout du nouveau dossier pdfs pour l'autoload
						->addResourceType('pdf', 'pdfs/', 'Pdf');
	 
		return $ressourceLoader;
	}
	
	/**
     * Initilisattion des traductions
     * 
     * @return array
     */
    public function _initTranslate() {
		//Traduction des messages ( messages d'erreur )
		$translate = new Zend_Translate('array', APPLICATION_PATH . '/languages/fr_FR.php', 'fr_FR');
        return $translate;
        
        $dbAdapter = Zend_Db::factory($configuration->database);
		$dbAdapter->query('SET NAMES UTF8');
		$dbAdapter->setFetchMode(Zend_Db::FETCH_OBJ);;
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);  
    }  
    
}

