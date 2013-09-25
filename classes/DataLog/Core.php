<?php defined('SYSPATH') OR die('No direct script access.');

class DataLog_Core {

	/** @var array */
	protected $old_values;

	/** @var string The suffix used to designate foreign key fields. */
	protected $foreign_key_suffix = '_id';

	public function __construct($table_name, $original_values)
	{
		$this->table_name = $table_name;
		$this->old_values = $original_values;
	}

	public function save($row_id, $object, $related)
	{
		//var_dump($object);exit();
		foreach ($object as $field => $new_datum)
		{
			// Handle foreign keys
			$prefix = substr($field, 0, -strlen($this->foreign_key_suffix));
			$related_columns = array_keys($related);
			if (isset($related_columns[$prefix]))
			{
				$old_datum = $this->old_values[$field];
				$foreign_model = $related->object_name();
				$old_value = ORM::factory($foreign_model, $old_datum)->candidate_key();
				$new_value = ORM::factory($foreign_model, $new_datum)->candidate_key();
			}
			// Handle normal direct values
			else
			{
				$old_value = $this->old_values[$field];
				$new_value = $new_datum;
			}
			// Save the log entry
			if ($new_value != $old_value)
			{
				$log_entry = ORM::factory('DataLog');
				$log_entry->table_name = $this->table_name;
				$log_entry->column_name = $field;
				$log_entry->row_id = $row_id;
				$log_entry->old_value = $old_value;
				$log_entry->new_value = $new_value;
				$log_entry->save();
			}
		}
	}

}
