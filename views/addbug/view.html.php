<?php
/**
 * @package    Joomla.J2Mantis
 * @subpackage Components
 * components/com_J2Mantis/view/view.html.php
 * @license    GNU/GPL
*/
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the J2Mantis Component
 *
 * @package    J2Mantis
 */
 
class J2MantisViewaddbug extends JView
{
    function display($tpl = null)
    {
		require_once( JPATH_COMPONENT_SITE.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT_SITE.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);

		$findIds = $settings->getMantisProjectIds();
		if( empty( $findIds ) || (sizeof($findIds)==1 && $findIds[0] == 0) ){		
			foreach($Mantis->getAllProjects(false, true) as $id => $p){			
				$settings->addMantisProjectId($id); 
			}
		}

		$project = $Mantis->getAllProjects();
		$this->assignRef('project', $project );
		$cat = $Mantis->getAllCategoriesOfProject();
        $this->assignRef( 'cat', $cat);

		$params = &JComponentHelper::getParams( 'com_j2mantis' );
		$defCaption = $params->get('caption');
 		
    	$caption = ($defCaption) ? $defCaption : JText::_('New problem added'); //"Neues Problem hinzufügen";
        $this->assignRef( 'caption', $caption );
        parent::display($tpl);
    }
}

?>
