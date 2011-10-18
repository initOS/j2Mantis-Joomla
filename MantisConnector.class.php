<?php
/**
 * Handle the connection with Mantis and make the SOA Calls
 */
class MantisConnector{
	/**
	 * JoomlaMantisParameter.class instance 
	 */
	private $settings;
	
	/**
	 * basic consturctor for the Connector
	 * @param JoomlaMantisParameter
	 * @return void
	 */
	function __construct(&$JoomlaMantisParameter){
		$this->settings = $JoomlaMantisParameter;
	}
	
   /**
	* Add attachment to mantis bug report.
	* @param object Class with attachment data inside.
	* @return mixed Returns false if attaching failed, number of issue otherwise.
	*/
	public function addAttachment( $attachment ){
		$client = new soapclient($this->settings->getWsdlUrl());
		
		//webservice might throw exception...
		try{
			$result = $client->mc_issue_attachment_add($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $attachment->id, $attachment->name, 
				$attachment->file_type, $attachment->content); 
		}
		catch (Exception $e){
            Logging::getInstance()->logException($e);
			return false;
		}
		return $result;
	}


    

    
    
    protected function setStatusToReOpen($mantisId){
        $client = new soapclient($this->setting->getWsdlUrl());
        $getBug = $client->mc_issue_get($this->setting->getMantisUser(),$this->setting->getMantisPassword(), $mantisId);
        if($getBug->status->id <= 20){
            return;
        }
        $getBug->status->id = 20;
        try{
        	$client->mc_issue_update($this->setting->getMantisUser(),$this->setting->getMantisPassword(), $mantisId, $getBug);
        }catch (Exception $e){
        	return false;	
        }
    }
    
    /**
     * get all categories of the project define in the settings
     *
     * @return ArrayString
     */
	public function getAllCategoriesOfProject(){
		try{
			$client = new soapclient($this->settings->getWsdlUrl());
		}catch (Exception $e){
        	return false;	
        }
        require_once( JPATH_COMPONENT_SITE.DS.'soa_objects'.DS.'bug_data.php');
		foreach( $this->settings->getMantisProjectIds() as $id ){        
			try{
		    	$getArray[$id] = $client->mc_project_get_categories($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $id );
		    }catch (Exception $e){
		    	$getArray[$id] = array();	
		    }
		}
        return $getArray;
    }
 

    /**
     * get all definition of projects define in the settings
     *
     * @return ArrayObject
     */
	public function getAllProjects($withSubProjects = true, $noCheck = false){
		try{
			$client = new soapclient($this->settings->getWsdlUrl());
		}catch (Exception $e){
        	return false;	
        }
        require_once( JPATH_COMPONENT_SITE.DS.'soa_objects'.DS.'bug_data.php');
		try{
		    	$getArray = $client->mc_projects_get_user_accessible($this->settings->getMantisUser(),$this->settings->getMantisPassword() );
	    }catch (Exception $e){
			//var_dump($e);	
			$getArray = array();	
	    }		
		//var_dump($getArray);
		$returnArray = array();
		while( !empty($getArray) ){  
			$project = array_pop($getArray);
			if( ( in_array( $project->id  , $this->settings->getMantisProjectIds() ) || $noCheck )
				&& $project->enabled ){
				$returnArray[$project->id] = $project->name;
				if( $withSubProjects ){
					foreach( $project->subprojects as $p ){
						$p->name = $project->name . " >> " . $p->name;
						$p->parrentId = $project->id;
						$this->settings->addMantisProjectId($p->id);
						array_push($getArray, $p );
					}
				}

			}
			
		}      
			
		
        return $returnArray;
    }
   
    /**
     * give all Bugs from the current Project store in the Settings
     *
     * @return array BugData
     */
	public function getAllBugsOfProject(){
    	$client = new soapclient($this->settings->getWsdlUrl());
        require_once( JPATH_COMPONENT_SITE.DS.'soa_objects'.DS.'bug_data.php');
    	try{
    		$getBugArray = $client->mc_project_get_issues($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $this->settings->getMantisProjectId());
    	}catch (Exception $e){
        	return false;	
        }

        return $getBugArray;
    }

    /**
     * give all Bugs from the current Project store in the Settings
     *
     * @return array BugData
     */
	public function getAllBugsOfAllProjects(){
    	$client = new soapclient($this->settings->getWsdlUrl());
        require_once( JPATH_COMPONENT_SITE.DS.'soa_objects'.DS.'bug_data.php');
		$getBugArray = array();	
		foreach($this->settings->getMantisProjectIds() as $id){   	
			try{
				$getBugArray =  array_merge($getBugArray, $client->mc_project_get_issues($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $id ));
			}catch (Exception $e){
		    	//return false;
				//var_dump($e);
		    }
		}

        return $getBugArray;
    }
    
    /**
     * get a Bug by hi Id
     *
     * @param int $bugID
     * @return BugData
     */
    public function getBug($bugID){
    	require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'bug_data.php');
    	if(empty($bugID)){
    		return new BugData();
    	}
    	
    	$client = new soapclient($this->settings->getWsdlUrl());
        try{
        	$getBug = $client->mc_issue_get($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $bugID);
        }catch (Exception $e){
        	return false;	
        }
        return $getBug;
    }
    
	/**
	* Add bug report to mantis using webservice.
	* @param object Class with bug report data inside.
	* @return mixed Returns false if attaching failed, number of issue otherwise.
	*/
    public function addBug($bug) {
        require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'project_data.php');
    	$client = new soapclient($this->settings->getWsdlUrl());
		//webservice might throw exception...
		try{
			$result = $client->mc_issue_add($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $bug);
		}
		catch (Exception $e){
            //var_dump($e);
			return false;
		}
        
        return $result;
    }
    
    /**
     * add a Note to this Bug
     *
     * @param int $bugId
     * @param string $text
     * @return multible, false on error
     */
    public function addNote($bugId, $text){
    	$client = new soapclient($this->settings->getWsdlUrl());
        
    	require_once( JPATH_COMPONENT.DS.'soa_objects'.DS.'note.php');
        $note = new Note();
        $note->text = $text;
        
        //webservice might throw exception...
        try{
            $result = $client->mc_issue_note_add($this->settings->getMantisUser(),$this->settings->getMantisPassword(), $bugId, $note);
        }
        catch (Exception $e){
            //var_dump($e);
            return false;
        }
        return $result;
    }
    
	
	function encode($text, $key = 'S4Gengu6weopgrk')
	{
		$somekey = $this->settings->getKey();
		if(!empty($somekey)){
			$key = $this->settings->getKey();
		}
		$l_k = strlen($key);
		$l_t = strlen($text);
		
		if($l_k == 0) return $text; // Ohne Key keine Verschlüsselung!!!
		
		$encoded = "";
		$k = 0; // Position im Key
		for($i=0; $i<$l_t; $i++)
		{
			if($k > $l_k) $k = 0; // Wenn ende des keys, dann wieder von vorne
			$encoded .= chr(ord($text[$i]) ^ ord($key[$k])); // Verschlüsselung
			$k++;
		}
		return $encoded;
	}
}
?>
