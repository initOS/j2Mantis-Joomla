<?php
/**
 * @package    Joomla.J2Mantis
 * @subpackage Components
 * components/com_J2Mantis/JoomlaMantisParameter.class.php
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Contains all configuration parameter 
 * 
 * @author Tobias Kalbitz
 */
class JoomlaMantisParameter {

  /**
   * The project name of bugs
   * 
   * @var string
   */
  private $mantisProjectName;
  
  /**
   * the project id of bugs
   *
   * @var int
   */
  private $mantisProjectId;

  /**
   * multiple project ids
   *
   * @var int of array
   */
  private $mantisProjectIds;

  /**
   * The name of the custome field which contain the name of a customer.
   * 
   * @var string
   */
  private $mantisCustomerField;
  
  /**
   * The user to login into mantis
   * 
   * @var string
   */
  private $mantisUser;
  
  /**
   * The passwort to login into mantis.
   * 
   * @var string
   */
  private $mantisPassword;

	/**
	 * The caption for form
	 *
	 * @var string
	 */
	private $mantisCaption;

  /**
   * The URL to the websersice description file
   *
   * @var string
   */
  private $wsdlUrl;
  
  private $key;
  
  /**
   * basic constructor
   *
   * @param string $url the wsdl url
   * @param string $user Mantis Username
   * @param string $passwd Password of the Mantis Account
   * @param string $name The name of the Projekt
   */
	function __construct($name = '', $isAdmin = false)
	{
		// configuration params, combining parameters from "component" and "menu" level
		$app = JFactory::getApplication();
		if ($app->isSite()) {
			// when on 'site' merge menu and 'component'
			$params = $app->getParams('com_j2mantis');
		} else {
			$params = &JComponentHelper::getParams('com_j2mantis');
		}

		$this->wsdlUrl = $params->get('url');
		$this->mantisUser = $params->get('username');
		$this->mantisPassword = $params->get('password');
		$this->mantisProjectIds = preg_split('/,/', $params->get('project'));
		$this->mantisProjectId = $this->mantisProjectIds[0];
		$this->key = $params->get('key');
		$this->mantisProjectName = $name;
		$this->mantisCaption = $params->get('caption');
	}

	/**
   * @see $mantisProjectName
   * @return string
   */
  public function getMantisProjectName() {
    return $this->mantisProjectName;
  }
  
  public function getMantisProjectId(){
  	return $this->mantisProjectId;
  }
  
  public function setMantisProjectId($id){
		if( in_array($id, $this->mantisProjectIds ) ){
			$this->mantisProjectId = $id;
		}
  }

  public function getMantisProjectIds(){
  	return $this->mantisProjectIds;
  }

  public function addMantisProjectId($id){
	if( !in_array( $id, $this->mantisProjectIds ) )	
	 array_push( $this->mantisProjectIds, $id);
  }

  /**
   * @see $mantisCustomerField
   * @return string
   */
  public function getMantisCustomerField() {
    return $this->mantisCustomerField;
  }
  
  /**
   * @see $mantisUser
   * @return string
   */
  public function getMantisUser() {
    return $this->mantisUser;  
  }
  
  /**
   * @see $mantisPassword
   * @return string
   */
  public function getMantisPassword() {
    return $this->mantisPassword;  
  }

  /**
   * @see $mantisCaption
   * @return string
   */
  public function getMantisCaption() {
	return $this->mantisCaption;
  }

  /**
   * @see $wsdlUrl
   * @return string
   */
  public function getWsdlUrl() {
    return $this->wsdlUrl;
  }
  
  public function getKey(){
  	return $this->key;
  }
}
?>
