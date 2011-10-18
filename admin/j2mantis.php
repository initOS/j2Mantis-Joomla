<?php
/**
 * @package    Joomla.J2Mantis
 * @subpackage Components
 * components/com_J2Mantis/J2Mantis.class.php
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ErrorHandler extends Exception {
    protected $severity;
   
    public function __construct($message, $code, $severity, $filename, $lineno) {
        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;
    }
   
    public function getSeverity() {
        return $this->severity;
    }
}

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    //var_dump($errfile);
	//throw new ErrorHandler($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler", E_WARNING);

JToolBarHelper::preferences( 'com_j2mantis' ,'300');
JToolBarHelper::title('j2Mantis');

echo "<h2>Your current settings</h2>";

 $params = &JComponentHelper::getParams( 'com_j2mantis');
 
echo "<strong>Mantis wsdl Url:</strong> " . $params->get('url') . "<br/>";
echo "<strong>Mantis User:</strong> " . $params->get('username') . "<br/>";
// JPATH_COMPONENT_SITE

require_once( JPATH_COMPONENT_SITE.DS.'JoomlaMantisParameter.class.php');
$settings = new JoomlaMantisParameter();
//check if the url is good
$page = simplexml_load_file($settings->getWsdlUrl());
//var_dump($page->attributes()->targetNamespace);
if($page && $page->attributes()->targetNamespace == 'http://futureware.biz/mantisconnect'){
	require_once( JPATH_COMPONENT_SITE.DS.'MantisConnector.class.php');
	echo "Connection: <span style='color: #090; font-weight: bold;'>works</span><br/>";
	$Mantis = new MantisConnector($settings);
	$projects = $Mantis->getAllProjects(true,true);
	if($projects){
		echo "Loggin: <span style='color: #090; font-weight: bold;'>works</span><br/>";
		echo "access to the following projects: <br/>";
		foreach($projects as $id => $p){
			echo "- ". $p . " ( ".$id." )<br/>";
		}
	}else{
		echo "Loggin: <span style='color: #900; font-weight: bold;'>dont work</span><br/>";
	}
}else{
	echo "Connection: <span style='color: #900; font-weight: bold;'>dont work</span><br/>";
}
?>
