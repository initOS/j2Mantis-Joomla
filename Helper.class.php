<?php
/**
 * User: Marco
 * Date: 2/22/13
 * Time: 3:59 PM
 * To change this template use File | Settings | File Templates.
 */
class J2MantisHelper {

	static $viewLevels;

	public static function getJ2M_Status($bug)
	{
		$j2m=array();
		$additional_information=$bug->additional_information;
//		$aaa['action']="actiehouder1";$aaa['state']="assigned";$bbb['action']="ac\"tiehouder2";$bbb['other']="anders";
//		$additional_information=$additional_information."#J2M#v1.0#".json_encode($aaa)."#J2M#\n";
//		$additional_information=$additional_information."asdfadsf asd\n";
//		$additional_information=$additional_information."#J2M#v1.0#".json_encode($bbb)."#J2M#";
		// #J2M#v1.0#  #J2M#
		$tomatch="/#J2M#v(\d+\.\d+)#(.*?)#J2M#/sm";
		preg_match_all($tomatch, $additional_information, $matches);
		$max_matches=count($matches[0]);
		for($idx=0; $idx<$max_matches; $idx++)
		{
			if ( $matches[1][$idx] == '1.0' ) {
				$j2m=array_merge((array)json_decode($matches[2][$idx]),$j2m);
			}
		};
		return $j2m;
	}

	public static function setJ2M_Status($bug, $j2m)
	{
		$additional_information=$bug->additional_information;
		$bug->additional_information=$additional_information."\n#J2M#v1.0#".json_encode($j2m)."#J2M#";
	}
	public static function FilterJ2M_Status($bug)
	{
		$tomatch="/#J2M#v(\d+\.\d+)#(.*?)#J2M#\n?/sm";
		$additional_information=$bug->additional_information;
		return preg_replace($tomatch, "", $additional_information);;
	}



	public static function getAuthorisedViewLevelsUsers($viewlevel)
	{
		// Only load the view levels once.
		if (empty(self::$viewLevels))
		{
			// Get a database object.
			$db = JFactory::getDBO();

			// Build the base query.
			$query = $db->getQuery(true);
			$query->select('id, rules');
			$query->from($query->qn('#__viewlevels'));

			// Set the query for execution.
			$db->setQuery((string) $query);

			// Build the view levels array.
			foreach ($db->loadAssocList() as $level)
			{
				self::$viewLevels[$level['id']] = (array) json_decode($level['rules']);
			}
		}
		$users=array();
		foreach (self::$viewLevels[$viewlevel] as $group) {
			$users= array_merge($users,JAccess::getUsersByGroup($group));
		}
		return $users;
	}
}

