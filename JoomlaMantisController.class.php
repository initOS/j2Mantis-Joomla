<?php
/**
 * @package    Joomla.J2Mantis
 * @subpackage Components
 * components/com_J2Mantis/JoomlaMantisController.class.php
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// import controller
jimport('joomla.application.component.controller');



/**
 * Main Controller for J2Mantis Component
 */
class JoomlaMantisController extends JController
{
	/**
	 * basic constructor
	 */
	function __construct(){	    
		parent::__construct(array('name'=>'J2Mantis'));
		$this->registerTask('addBug','addBug');
		$this->registerTask('addNote','addNote');
	}
	/**
	 * add a Bug from the Request to Mantis using SOA
	 *
	 */
	function addBug(){
		
		if($_POST['check']!=JUtility::getToken()) {
		        //First verify if by a javascript error or other possibilities the form has not been submitted without the validation
		        if ($_POST['check']=='post')
		        	$errors[] = 'Bitte aktivieren sie Javascript!';
		        //If then still the check isn't a valid token, do nothing as this might be a spoof attack or other invalid form submission
		        //return false;
		}
		$app = JFactory::getApplication('site');
        $params =  & $app->getParams('com_j2mantis');
		$errors = array();

		if($params->get('captcha') && !plgSystemJCCReCaptcha::confirm( $_POST['recaptcha_response_field'] )){
			$errors[] =  "captcha falsch!";
		}
		
		$summary = JRequest::getString('summary','');
		$category = JRequest::getString('category','');
		$description = JRequest::getString('description','');
		$additional_information = JRequest::getString('name','') . " ( " . JRequest::getString('email','') . " )";
		$priority = JRequest::getInt('priority', 30);
		$projectId = JRequest::getInt('project',0);
        $due_date = $app->input->get('due_date', null, 'STR' );

		if(count($errors) > 0){
			$_POST['errors'] = $errors;
			parent::display();
			return;
		}
		
		require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'bug_data.php');
		$newBug = new BugData();
		$newBug->summary = $summary;
		$newBug->due_date = $due_date;
		$newBug->category = $category;
		$newBug->description = $description;
		$newBug->additional_information = $additional_information;
		require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'project_data.php');
		$pr = new ProjectData();
		$pr->id = $projectId;
		$newBug->project = $pr;
        $newBug->priority = $priority;
		$newBug->severity = 50;
		$newBug->status = 10;
		$newBug->reproducibility = '';
		$newBug->resolution = '';
		$newBug->projection = 10;
		$newBug->eta = 10;
		$newBug->view_state = 10;
		require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();

		if($projectId != 0 ){
			$settings->setMantisProjectId( $projectId );
		}

		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);

		//var_dump($settings);
		if($result = $Mantis->addBug($newBug)){
			$result = base64_encode($Mantis->encode((string)$result));
			$this->setRedirect('index.php?option=com_j2mantis&view=showbug&bugid='.$result.'&Itemid='.JRequest::getInt('Itemid',0));
		}else{
			$to = $params->get('email');
			$text = $newBug->description . "\n";
			$text .= $newBug->category . "\n";
			$text .= $newBug->additional_information . "\n";
			$text .= $newBug->summary . "\n\n\n";
			$text .= "Note: this email was sent by j2Mantis, becouse the webservice on ".$params->get('url')." dosn't work";
			if(mail($to, 'j2Mantis request', $text)){
				echo "Dein Anliegen wurde per EMail übermittelt";
				return;
			}else{
				echo "Es ist ein Fehler aufgetreten";
				return;
			}
			
		}
	}
	
	function addFile(){
		$bugid = JRequest::getInt('bugid', 0);
		if($bugid == 0){
			echo "kein BugId gegeben";
		}

		require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'bug_attachment.php');
		$att = new BugAttachment();
		$att->id = $bugid;
		$att->name = $_FILES['name']['name'];
		$att->file_type = $_FILES['name']['type'];
        jimport( 'joomla.filesystem.file' );
		$att->content = JFile::read($_FILES['name']['tmp_name']);
		
		require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);
		if($Mantis->addAttachment($att)){
			$bugid = base64_encode($Mantis->encode((string)$bugid));
			$this->setRedirect('index.php?option=com_j2mantis&view=showbug&bugid='.$bugid.'&Itemid='.JRequest::getInt('Itemid',0));
		}else{
			echo "Es ist ein Fehler aufgetreten";
		}
	}
	
	/**
	 * add a Note to a Bug from the Request to Mantis while using SOA
	 *
	 */
	function addNote(){
		$bugid = JRequest::getInt('bugid', 0);
		if($bugid == 0){
			echo "kein BugId gegeben";
		}
		$text = '';
		$name = JRequest::getString('name');
		if ( !empty( $name ) ) {
			$text .= $name . ":\n";
		}
		$text .= JRequest::getString('text');
		
		require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);
		if($Mantis->addNote($bugid, $text)){
			$bugid = base64_encode($Mantis->encode((string)$bugid));
			$this->setRedirect('index.php?option=com_j2mantis&view=showbug&bugid='.$bugid.'&Itemid='.JRequest::getInt('Itemid',0));
		}else{
			echo "Es ist ein Fehler aufgetreten";
		}
	}

	/**
	 * add a Note to a Bug from the Request to Mantis while using SOA
	 *
	 */
	function editBug(){
		$app = JFactory::getApplication('site');
		require_once( JPATH_COMPONENT.DS.'JoomlaMantisParameter.class.php');
		$settings = new JoomlaMantisParameter();
		require_once( JPATH_COMPONENT.DS.'MantisConnector.class.php');
		$Mantis = new MantisConnector($settings);

		$bugid = $app->input->get('bugid', null, 'INT' );
		if( empty($bugid) || $bugid == 0){
			echo "no associated bugid";
		}
		$bug = $Mantis->getBug( $bugid );
		$due_date = $app->input->get('due_date', null, 'STR' );
		if( empty($due_date) || is_null($due_date)){
			$due_date = "";
		}
		// due_date in format Y-m-d, 'm-d-y H:i T'??, what dateformat is configured to be used ?
		$bug->due_date = $due_date ;
		if( $Mantis->setBug($bugid,$bug)){
			$EncodedBugId = base64_encode($Mantis->encode((string)$bugid));
			$Itemid=JRequest::getInt('Itemid',0);
			$this->setRedirect('index.php?option=com_j2mantis&view=showbug&bugid='.$EncodedBugId.'&Itemid='.$Itemid);
		}else{
			echo "Oops someething went wrong contacting mantis";
		}
	}

    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable, $urlparams);

        return $this;
    }
 
}
?>
