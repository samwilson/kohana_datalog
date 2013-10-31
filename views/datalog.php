<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @param $caption
 * @param $show_table
 * @param $datalog
 */
?>

<table class="datalog">

	<caption>
	<?php echo (isset($caption)) ? $caption : __(Kohana::message('datalog', 'table_caption')) ?>
	</caption>

	<thead>
		<tr>
			<th>Date &amp; time</th>
			<th>User</th>
			<?php if ($show_table) echo '<th>Record Type</th><th>Record ID</th>' ?>
			<th>Field</th>
			<th class="old">Old value</th>
			<th></th>
			<th class="new">New value</th>
		</tr>
	</thead>

	<tbody>
			<?php foreach ($datalog as $log): ?>
		<tr>
			<td><span class="datetime-convert"><?php echo $log->date_and_time ?></span></td>
			<td><?php echo $log->username ?></td>

			<?php if ($show_table): ?>
			<td><?php echo ucwords(str_replace('_', ' ', $log->table_name)) ?></td>
			<td><?php echo $log->row_pk ?></td>
			<?php endif ?>

			<td class="column_name"><?php echo ucwords(str_replace('_', ' ', $log->column_name)) ?></td>
			<td class="old"><?php echo $log->old_value ?></td>
			<td>&rArr;</td>
			<td class="new"><?php echo $log->new_value ?></td>
		</tr>
		<?php endforeach ?>

		<?php if (count($datalog)==0) echo '<tr><td colspan="*"></td></tr>' ?>

	</tbody>
</table>