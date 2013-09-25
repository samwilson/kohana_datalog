DataLog module for Kohana
=========================

This module is for keeping a log of changes made to ORM data.
When added to an ORM model, it records who made what changes, when.

It stores actual values of the data (and usernames)
rather than references to other parts of the database
so that it can be as simple and robust as possible.
This way it can be used with schema changes over time
and still keep historical data.

## Installation and Configuration

It requires a single database table (named `datalog`), that has the following
columns: `id`, `date_and_time`, `table_name`, `column_name`, `row_pk`,
`username`, `old_value`, and `new_value`.

Run `php index.php datalog` to create the database table.

## Usage

### Saving:

For the model that you want to log, you need to override `ORM::save()`,
so that DataLog can access the model's data before and after being saved.

	public function save(Validation $validation = NULL)
	{
		$datalog = new DataLog($this->_table_name, $this->_original_values);
		$parent = parent::save($validation);
		$datalog->save($this->pk(), $this->_object, $this->_related);
		return $parent;
	}

If DataLog needs to apply to *all* ORM models,
then the above should be added to `APPPATH/classes/ORM.php`

#### Foreign Keys:

Foreign keys should be suffixed with `_id`.
This is set by `DataLog::$foreign_key_suffix`.

Models that are related to this one should implement a `candidate_key()` method
that returns a string representation of a human-readable identifier for the
loaded row. For a model with a unique `name` column, this can be as simple as 

	public function candidate_key()
	{
		if (!$this->loaded()) return FALSE;
		return $this->name;
	}

The static value of the related record's candidate key is used in preferance to
looking it up dynamically so that the datalog is kept completely self-contained
and doesn't constrain future redesigns of the models in question.

If the primary key does need to be used in the log,
then `candidate_key()` should be defined to return it.

If no `candidate_key()` method is defined.... @TODO

### Viewing:

You can get an HTML table view of a model record's datalog
with a simple subrequest:

	$datalog_url = "datalog/$table_name/$row_pk";
	$datalog = Request::factory($datalog_url)->execute()->body();

This view is restricted to subrequests only, and is not accessible directly
(at e.g. `/datalog/model/1`).
