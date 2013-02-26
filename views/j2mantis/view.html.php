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
 
class J2MantisViewj2mantis extends JView
{
	var $sort_on;
	var $sort_order;

	function issue_compare($a, $b) {
		$on=$this->sort_on;
		if ( in_array( $on, array('priority', 'severity', 'status', 'project', 'handler', 'reporter', 'resolution' ) ) ) {
			$aa=$a->$on->id;
			$bb=$b->$on->id;
		} elseif ( in_array( $on, array( 'last_updated', 'due_date', 'summary', 'date_submitted', 'additional_information', 'description' ) ) ) {
			// add configuration item to make searching case sensative
			if ( $this->sort_case ) {
				$aa=strtolower($a->$on);
				$bb=strtolower($b->$on);
			}
			else {
				$aa=$a->$on;
				$bb=$b->$on;
			}
		} else { // 'id', 'sticky'
			$aa=$a->$on;
			$bb=$b->$on;
		}
		if ($aa == $bb) {
			return 0;
		}
		return ($aa < $bb) ? -$this->sort_order : $this->sort_order;
	}

	/**
	 * Sort the issue list
	 *
	 * @param $array
	 * @param string $sort_on : field from IssueData to sort on, 'NONE' implies no sorting
	 * @param int $sort_order
	 * @return mixed
	 */
	function issue_array_sort($array, $sort_on='last_updated', $sort_order=1, $sort_case=0 )
	{
		$this->sort_on=$sort_on;
		$this->sort_order=$sort_order;
		$this->sort_case=$sort_case;
		if ( (count($array) > 0) && ( $sort_on != 'NONE' ) ) {
			uasort($array, array($this, 'issue_compare'));
		}
		return $array;
	}

    function display($tpl = null)
    {
		$app = &JFactory::getApplication();
		if ($app->isSite()) {
			// when on 'site' merge menu and 'component'
			$params = $app->getParams('com_j2mantis');
		} else {
			$params = &JComponentHelper::getParams('com_j2mantis');
		}

		$overview = $params->get('overview');
		if(empty($overview)){
			echo "no overview allowed";
    		return;
		}
    	
    	require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);

		$findIds = $settings->getMantisProjectIds();
		if( empty( $findIds ) || (sizeof($findIds)==1 && $findIds[0] == 0) ){		
			foreach($Mantis->getAllProjects(false, true) as $id => $p){			
				$settings->addMantisProjectId($id); 
			}
		}

		$bugs = $Mantis->getAllBugsOfAllProjects();
		$bugs = $this->issue_array_sort($bugs,$params->get('sort_on'), $params->get('sort_order'), $params->get('sort_case'));
		$hasactionholders=false;
		$hasduedate=false;
		foreach( $bugs as $bug ) {
			$bug->j2m=J2MantisHelper::getJ2M_Status($bug);
			$hasactionholders=($hasactionholders)||($bug->j2m[actionholder]);
			$hasduedate=($hasduedate)||($bug->due_date);
		}
		$this->hasactionholders=$hasactionholders;
		$this->hasduedate=$hasduedate;
		$this->bugs = $bugs;
        $this->mantis=$Mantis;
		$this->moduleclass_sfx=$params->get('moduleclass_sfx',"");

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

		$this->caption = ($this->defCaption) ? $this->defCaption : JText::_('Report Overview');

        parent::display($tpl);
    }
}

?>