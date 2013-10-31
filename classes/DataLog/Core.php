<?php defined('SYSPATH') OR die('No direct script access.');

class DataLog_Core {

	/**
	 * @var array The name of the table being logged.
	 */
	protected $_table_name;

	/**
	 * @var array Original values of model being logged
	 */
	protected $_old_values;

	/**
	 * Constructs a new instance of DataLog
	 *
	 * @param string $table_name Table name for model being logged
	 * @param array  $old_values $_original_values property for model being logged
	 */
	public function __construct($table_name, $old_values)
	{
		$this->_table_name = $table_name;
		$this->_old_values = $old_values;
	}

	/**
	 * Check the field to see if it is a foreign key to another model
	 * and return the foreign model's name if it is.
	 *
	 * @param  string $field      Field to search for
	 * @param  array  $belongs_to The belongs-to relationships of the model being saved
	 * @return string Name of foreign model
	 */
	protected function _foreign_model_search($field, array $belongs_to = NULL)
	{
		if (is_array($belongs_to))
		{
			foreach ($belongs_to as $value)
			{
				if ($value['foreign_key'] == $field)
				{
					return $value['model'];
				}
			}
		}
		return FALSE;
	}

	/**
	 * Check data being saved and create log entries for modified values.
	 *
	 * @param  int    $row_pk     Primary Key of model being saved
	 * @param  array  $object     Current values of the ORM object
	 * @param  array  $belongs_to The belongs-to relationships of the model being saved
	 * @return string Name of foreign model
	 */
	public function save($row_pk, $object, array $belongs_to = NULL)
	{
		foreach ($object as $field => $new_datum)
		{
			// Check if current field is a foreign key
			$foreign_model = $this->_foreign_model_search($field, $belongs_to);

			// If foreign model exists set value using candidate_key value
			if ($foreign_model !== FALSE)
			{
				$old_datum = Arr::get($this->_old_values, $field, NULL);
				$old_value = ORM::factory($foreign_model, $old_datum)->candidate_key();
				$new_value = ORM::factory($foreign_model, $new_datum)->candidate_key();
			}
			// Handle normal direct values
			else
			{
				$old_value = Arr::get($this->_old_values, $field, NULL);
				$new_value = $new_datum;
			}

			// Save the log entry
			if ($new_value != $old_value)
			{
				$log_entry = ORM::factory('DataLog');
				$log_entry->table_name = $this->_table_name;
				$log_entry->column_name = $field;
				$log_entry->row_pk = $row_pk;
				$log_entry->old_value = $old_value;
				$log_entry->new_value = $new_value;
				$log_entry->save();
			}
		}
	}

}
