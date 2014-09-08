<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanDatabaseTableExtensions extends KDatabaseTableDefault
{
	protected $_row_package;

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'behaviors'  => 'creatable',
			'filters'    => array(
				'manifest'   => 'com://admin/extman.filter.manifest',
				'identifier' => 'com://admin/extman.filter.identifier'
			)
		));

		return parent::_initialize($config);
	}

    public function select( $query = null, $mode = KDatabase::FETCH_ROWSET)
    {
       //Create query object
        if(is_numeric($query) || is_string($query) || (is_array($query) && is_numeric(key($query))))
        {
            $key    = $this->getIdentityColumn();
            $values = (array) $query;

            $query = $this->_database->getQuery()->where($key, 'IN', $values);
        }

        if(is_array($query) && !is_numeric(key($query)))
        {
            $columns = $this->mapColumns($query);
            $query   = $this->_database->getQuery();

            foreach($columns as $column => $value) {
                $query->where($column, 'IN', $value);
            }
        }

        if($query instanceof KDatabaseQuery)
        {
            if(!is_null($query->columns) && !count($query->columns)) {
                $query->select('*');
            }

            if(!count($query->from)) {
                $query->from($this->getName().' AS tbl');
            }
        }

        //Create commandchain context
        $context = $this->getCommandContext();
        $context->operation = KDatabase::OPERATION_SELECT;
        $context->query     = $query;
        $context->table     = $this->getBase();
        $context->mode      = $mode;

        if($this->getCommandChain()->run('before.select', $context) !== false)
        {
            //Fetch the data based on the fecthmode
            if($context->query)
            {
                $data = $this->_database->select($context->query, $context->mode, $this->getIdentityColumn());

                //Map the columns
                if (($context->mode != KDatabase::FETCH_FIELD) || ($context->mode != KDatabase::FETCH_FIELD_LIST))
                {
                    if($context->mode % 2)
                    {
                        foreach($data as $key => $value) {
                            $data[$key] = $this->mapColumns($value, true);
                        }
                    }
                    else $data = $this->mapColumns(KConfig::unbox($data), true);
                }
            }

            switch($context->mode)
            {
                case KDatabase::FETCH_ROW    :
                {
                    $options = array();
                    if(isset($data) && !empty($data))
                    {
                        $options = array(
                    		'data'   => $data,
                        	'new'    => false,
                            'status' => KDatabase::STATUS_LOADED
                        );
                    }

                    $context->data = $this->getRow($options);
                    break;
                }

                case KDatabase::FETCH_ROWSET :
                {
                    $context->data = $this->getRowset();
                    if(isset($data) && !empty($data)) {
                        $context->data->addData($data, false);
                    }
                    break;
                }

                default : $context->data = $data;
            }

            $this->getCommandChain()->run('after.select', $context);
        }

        return KConfig::unbox($context->data);
    }

    public function setRowPackage($package) {
    	$this->_row_package = $package;
    }

    public function getRow(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($this->getIdentifier()->name);

        if ($this->_row_package) {
        	$identifier->package = $this->_row_package;
        	$this->setRowPackage(null);
        }

        //The row default options
        $options['table'] = $this;
        $options['identity_column'] = $this->mapColumns($this->getIdentityColumn(), true);

        return $this->getService($identifier, $options);
    }
}