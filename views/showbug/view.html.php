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
		$caption = JText::_("problem description") ;
        $this->assignRef( 'caption', $caption );
        $this->assignRef( 'bug', $bug);
 
        parent::display($tpl);
    }
    

}

?>
