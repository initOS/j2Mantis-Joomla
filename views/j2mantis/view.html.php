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
 
class J2MantisViewj2mantis extends JView
{

	function array_sort($array, $on, $order='SORT_DESC') 
    { 
      $new_array = array(); 
      $sortable_array = array(); 
  
      if (count($array) > 0) { 
          foreach ($array as $k => $v) { 
              if (is_array($v)) { 
                  foreach ($v as $k2 => $v2) { 
                      if ($k2 == $on) { 
                          $sortable_array[$k] = $v2; 
                      } 
                  } 
              } else { 
                  $sortable_array[$k] = $v; 
              } 
          } 
  
          switch($order) 
          { 
              case 'SORT_ASC':    
                  //echo "ASC"; 
                  asort($sortable_array); 
              break; 
              case 'SORT_DESC': 
                  //echo "DESC"; 
                  arsort($sortable_array); 
              break; 
          } 
  
          foreach($sortable_array as $k => $v) { 
              $new_array[] = $array[$k]; 
          } 
      } 
      return $new_array; 
    } 

    function display($tpl = null)
    {
		$params = &JComponentHelper::getParams( 'com_j2mantis' );
		$overview = $params->get('overview');
		if(empty($overview)){
			echo "no overview allowd";
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
		$bugs = $this->array_sort($bugs, 'last_updated');
        $this->bugs = $bugs;
        $this->mantis=$Mantis;

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
