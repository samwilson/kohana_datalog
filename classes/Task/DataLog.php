<?php defined('SYSPATH') OR die('No direct script access.');

class Task_DataLog extends Minion_Task {

	protected function _execute(array $params)
	{
		$db = Database::instance();

		// Get the table name from the ORM model
		$table_name = ORM::factory('DataLog')->table_name();

		// Create the base table
		if ( ! in_array($table_name, $db->list_tables()))
		{
			$sql = "CREATE TABLE IF NOT EXISTS ".$db->quote_table($table_name)." (
				".$db->quote_column('id')." INT(6) NOT NULL AUTO_INCREMENT,
				".$db->quote_column('date_and_time')." DATETIME NOT NULL,
				".$db->quote_column('table_name')." VARCHAR(65) NOT NULL,
				".$db->quote_column('column_name')." VARCHAR(65) NOT NULL,
				".$db->quote_column('row_id')." INT(12) NOT NULL,
				".$db->quote_column('username')." VARCHAR(150) NOT NULL,
				".$db->quote_column('old_value')." TEXT,
				".$db->quote_column('new_value')." TEXT,
				PRIMARY KEY (".$db->quote_column('id').")
			)";
			Minion_CLI::write('Creating database table: '.$db->quote_table($table_name));
			$db->query(NULL, $sql);
		}

		// Change the row identifier,
		// because '_id' is meant to only be used for foreign keys
		$datalog_cols = $db->list_columns($db->table_prefix().$table_name);
		if (isset($datalog_cols['row_id']))
		{
			Minion_CLI::write('Changing row_id to row_pk');
			$sql = "ALTER TABLE ".$db->quote_table($table_name)."
				CHANGE COLUMN ".$db->quote_column('row_id')."
				".$db->quote_column('row_pk')." INT(12) NOT NULL";
			$db->query(NULL, $sql);
		}

	}

}
