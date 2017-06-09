<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class VereinOnlineViewVereinOnline extends JViewLegacy
{
	function display($tpl = null)
	{
		// Assign data to the view
		$this->calendarRows = $this->get('Items');
 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		// Display the view
		parent::display($tpl);
	}
}
?>