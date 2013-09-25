## Installation and Configuration

It requires a single database table (named `datalog`), that has the following
columns: `id`, `date_and_time`, `table_name`, `column_name`, `row_pk`,
`username`, `old_value`, and `new_value`.

Run `php index.php datalog` to create the database table.
