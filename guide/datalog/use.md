## Usage

DataLog needs to be set up to save data for each model that needs to be logged.
Logs can be viewed for everything, individual models, or individual records.

### Saving

For the model that you want to log, you need to override `ORM::save()`,
so that DataLog can access the model's data before and after being saved.

	public function save(Validation $validation = NULL)
	{
		$datalog = new DataLog($this->_table_name, $this->_original_values);
		$parent = parent::save($validation);
		$datalog->save($this->pk(), $this->_object, $this->_belongs_to);
		return $parent;
	}

If DataLog needs to apply to *all* ORM models,
then the above should be added to `APPPATH/classes/ORM.php`.
(The `save()` method in the DataLog model itself
doesn't call its parent's `save()` method,
so there's no problem with log entries being created for log entries.)

**Foreign Keys:**

Foreign keys that are represented in a model with a `$_belongs_to` relationship
will be saved in the log using the value of a candidate key from the foreign table.

These models must implement a `candidate_key()` method
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

### Viewing

You can get an HTML table view of a model record's datalog
with a simple subrequest:

	$datalog_url = Route::get('datalog')->uri(array('table_name' => $table_name, 'row_pk' => $row_pk));
	$datalog = Request::factory($datalog_url)->execute()->body();

`row_pk` is optional, and can be left out in order to retrieve all log entries
for a given table.

This view is restricted to subrequests only, and is not accessible directly
(at e.g. `/datalog/model/1`).
