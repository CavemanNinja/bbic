<?php 

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin'); jimport('joomla.log.log');
class plgAjaxSrdescription extends JPlugin
{
	function onAjaxSrdescription()
	{
	
		$query = $db->getQuery(true);
                $query->select($db->quoteName('attribs'));
                $query->from($db->quoteName('#__content'));
                $query->where($db->quoteName('id') . " = " . $service_id);
                $db->setQuery($query);
                $result = $db->loadResult();

                $service_attribs = json_decode($result);
                $service_description = $service_attribs->service_description;

		return json_encode($service_description);
	}
}