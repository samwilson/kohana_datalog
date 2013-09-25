<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'modules' => array(
		'datalog' => array(

			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'DataLog',

			// A short description of this module, shown on the index page
			'description' => "Logging of modifications to ORM models' data.",
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2013 Sam Wilson, GNU General Public License v3.0 or later',
		)
	)
);
