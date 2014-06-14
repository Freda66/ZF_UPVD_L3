<?php 
/**
 * Aide d'action de l'action courante
 * Permet de convertir une date dans le format d/m/Y
 * 
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Zend_View_Helper_ToDmy
{
    /**
     * Retourne une date au format d/m/Y
     * @param string $date
     */
    public function toDmy($date, $withTime = false)
    {
    	// Initialisation
    	$resultValue = null;
    	// Converti la date
    	$time = strtotime( $date );
    	$resultValue = date("d/m/Y", $time);
    	// Retourne le resultat
    	if($withTime) {
    		return $resultValue .' à '.substr($date,11,2).'h'.substr($date,14,2);
    	}
    	else return $resultValue;
    }
    
}