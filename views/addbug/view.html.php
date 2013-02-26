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

require_once( JPATH_COMPONENT_SITE.DS.'Helper.class.php');
 
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

		//
		$project = $Mantis->getAllProjects();
		$this->assignRef('project', $project );
		$cat = $Mantis->getAllCategoriesOfProject();
        $this->assignRef( 'cat', $cat);

		jimport('joomla.access.access');
		$params = JFactory::getApplication()->getParams('com_j2mantis');
		$action_acl = $params->get('access');
		$action_user_ids = J2MantisHelper::getAuthorisedViewLevelsUsers($action_acl);
		jimport('joomla.user.user');
		foreach ($action_user_ids as $action_user_id) {
			$this->actionholders[] = JFactory::getUser($action_user_id);
		}

		// assignRef no longer needed
		$this->user         = JFactory::getUser();
		$this->defCaption	= $settings->getMantisCaption();
		$this->fo_name		= $settings->getmantisFo_name();
		$this->fo_nameedit 	= $settings->getmantisFo_nameedit();
		$this->fo_email 	= $settings->getmantisFo_email();
		$this->fo_emailedit = $settings->getmantisFo_emailedit();
		$this->moduleclass_sfx=$params->get('moduleclass_sfx',"");
		if( $this->user->id==0 ){
			// no logged in user, then edit cannot be false if field required
			if ($this->fo_name  == 1 ) $this->fo_nameedit  = 1;
			if ($this->fo_email == 1 ) $this->fo_emailedit = 1;
		}

    	$this->caption = ($this->defCaption) ? $this->defCaption : JText::_('New problem added');

        parent::display($tpl);
    }
}

?>
