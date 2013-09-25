<?php defined('SYSPATH') OR die('No direct script access.');

Route::set('datalog', 'datalog/<table_name>/<row_pk>')->defaults(array(
	'controller' => 'DataLog',
	'action'     => 'index',
));
