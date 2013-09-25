<?php defined('SYSPATH') OR die('No direct script access.');

class Task_DataLog extends Minion_Task {

	protected function _execute(array $params)
	{
		$db = Database::instance();
		$sql = "CREATE TABLE IF NOT EXISTS ".$db->quote_table('datalog')." (
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
		Minion_CLI::write('Creating database table: '.$db->quote_table('datalog'));
		$db->query(NULL, $sql);
	}

}
