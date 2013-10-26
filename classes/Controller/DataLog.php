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
		// Get datalog
		$datalog = ORM::factory('DataLog')
			->where('table_name', '=', $this->request->param('table_name'))
			->and_where('row_pk', '=', $this->request->param('row_pk'))
			->order_by('date_and_time', 'DESC')
			->order_by('id', 'DESC')
			->find_all();

		if (count($datalog) > 0) {
			// Populate and return the view
			$view = View::factory('datalog');
			$view->datalog = $datalog;
			$this->response->body($view->render());
		}
	}

}
