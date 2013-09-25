<?php defined('SYSPATH') OR die('No direct script access.');

class Model_DataLog extends ORM {

	protected $_table_name = 'datalog';

	protected $_table_columns = array(
		'id' => array('type' => 'int'),
		'date_and_time' => array('type' => 'string'),
		'table_name' => array('type' => 'string'),
		'column_name' => array('type' => 'string'),
		'row_id' => array('type' => 'int'),
		'username' => array('type' => 'int'),
		'old_value' => array('type' => 'string', 'is_nullable' => TRUE),
		'new_value' => array('type' => 'string', 'is_nullable' => TRUE),
	);

	protected $_created_column = array(
		'column' => 'date_and_time',
		'format' => 'Y-m-d H:i:s'
	);

	/**
	 * Updates or creates the record depending on loaded(), setting the username
	 * to the current user's username and creating a matching Users record if
	 * one does not exist.
	 * 
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function save(Validation $validation = NULL)
	{
		$this->username = Auth::instance()->get_user();
		if (is_null($this->username))
		{
			$this->username = __('Anonymous');
		}
		return parent::save($validation);
	}

}
