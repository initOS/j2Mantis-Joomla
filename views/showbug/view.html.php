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
 
class J2MantisViewshowbug extends JView
{
    function display($tpl = null)
    {
		require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);

		$params = &JComponentHelper::getParams( 'com_j2mantis' );
		$bugid = $params->get('bug_id');
		if( empty($bugid) ){
			$bugid = JRequest::getVar('bugid',0);
			$bugid = $Mantis->encode(base64_decode(JRequest::getVar('bugid',0)));
		}
		//var_dump( $bugid );
		$bug = $Mantis->getBug( $bugid );
        $this->caption=$caption;
        $this->bug=$bug;
		$this->due_date = date( "Y-m-d",strtotime($bug->due_date));
/*
 * config_inc.php
 * $g_due_date_update_threshold = MANAGER;
 * $g_due_date_view_threshold = DEVELOPER;
 * */
// global option to support "due date" $support_duedate
// only display if $support_duedate set
//

		$this->user         = JFactory::getUser();
		$this->defCaption	= $settings->getMantisCaption();
		$this->fo_name		= $settings->getmantisFo_name();
		$this->fo_nameedit 	= $settings->getmantisFo_nameedit();
		$this->fo_email 	= $settings->getmantisFo_email();
		$this->fo_emailedit = $settings->getmantisFo_emailedit();
		if( $this->user->id==0 ){
			// no logged in user, then edit cannot be false if field required
			if ($this->fo_name  == 1 ) $this->fo_nameedit  = 1;
			if ($this->fo_email == 1 ) $this->fo_emailedit = 1;
		}

		$this->caption = ($this->defCaption) ? $this->defCaption : JText::_('Problem description');

        parent::display($tpl);
    }
    

}

?>
