<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanModelDependencies extends ComDefaultModelDefault
{
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		$state = $this->_state;

		if ($state->extman_extension_id) {
			$query->where('tbl.extman_extension_id', '=', $state->extman_extension_id);
		}

		if ($state->dependent_id) {
			$query->where('tbl.dependent_id', '=', $state->dependent_id);
		}
	}
}