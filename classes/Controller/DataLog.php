<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_DataLog extends Controller {

	public function before()
	{
		parent::before();
		if ($this->request->is_initial())
		{
			throw new HTTP_Exception_403;
		}
	}

	public function action_index()
	{
		$view = View::factory('datalog');
		$view->show_table = TRUE;

		// Get datalog
		$datalog = ORM::factory('DataLog')
			->where('table_name', '=', $this->request->param('table_name'));
		$row_pk = $this->request->param('row_pk');
		if ($row_pk != NULL)
		{
			$view->show_table = FALSE;
			$datalog->and_where('row_pk', '=', $row_pk);
		}

		$log_entries = $datalog->order_by('date_and_time', 'DESC')
			->order_by('id', 'DESC')
			->find_all();

		if (count($log_entries) > 0)
		{
			// Populate and return the view
			$view->datalog = $log_entries;
			$this->response->body($view->render());
		}
	}

}
