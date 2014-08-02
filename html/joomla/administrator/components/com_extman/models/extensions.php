<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanModelExtensions extends ComDefaultModelDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_state->insert('top_level', 'boolean', false)
					 ->insert('parent_id', 'int')
					 ->insert('event', 'cmd');
	}

	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if ($this->_state->top_level) {
			$query->where('tbl.parent_id = 0');
		}

		if ($this->_state->parent_id) {
			$query->where('tbl.parent_id', '=', $this->_state->parent_id);
		}
	}
}