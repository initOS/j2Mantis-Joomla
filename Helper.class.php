<?php
/**
 * User: Marco
 * Date: 2/22/13
 * Time: 3:59 PM
 * To change this template use File | Settings | File Templates.
 */
class J2MantisHelper {

	static $viewLevels;

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

