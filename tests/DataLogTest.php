<?php
/**
 * This file contains the test class itself, and three ORM models to test with.
 *
 * @file
 */
defined('SYSPATH') OR die('Bootstrap needs to be included before tests run.');

/**
 * DataLog tests. These are NOT unit tests, but full integration tests that
 * require a database with DataLog installed.
 *
 * Database tables are created in setup and droped in tear-down, and all data
 * modification inbetween is rolled back.
 *
 * @group    datalog
 * @package  DataLog
 * @category Tests
 * @author   Sam Wilson <sam@samwilson.id.au>
 */
class DataLogTest extends Unittest_TestCase {

	/**
	 * @var Database
	 */
	private $db;

	public function setUp()
	{
		parent::setUp();
		$this->db = Database::instance();

		$this->db->query(NULL, 'CREATE TABLE '.$this->db->quote_table('datalog_test_entities').' ('
			.$this->db->quote_column('id').' INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,'
			.$this->db->quote_column('type_id').' INT(10) NULL DEFAULT NULL,'
			.$this->db->quote_column('description').' TEXT NULL DEFAULT NULL'
			.')');
		$this->db->query(NULL, 'CREATE TABLE '.$this->db->quote_table('datalog_test_entity_info').' ('
			.$this->db->quote_column('id').' INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,'
			.$this->db->quote_column('entity_id').' INT(10) NULL DEFAULT NULL,'
			.$this->db->quote_column('size').' DECIMAL(5,2) NOT NULL DEFAULT 0.0'
			.')');
		$this->db->query(NULL, 'CREATE TABLE '.$this->db->quote_table('datalog_test_entity_types').' ('
			.$this->db->quote_column('id').' INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,'
			.$this->db->quote_column('name').' VARCHAR(100) NULL DEFAULT NULL'
			.')');
		$this->db->begin();
	}

	public function tearDown()
	{
		parent::tearDown();
		$db = Database::instance();
		$db->rollback();
		$db->query(NULL, 'DROP TABLE '.$this->db->quote_table('datalog_test_entity_info'));
		$db->query(NULL, 'DROP TABLE '.$this->db->quote_table('datalog_test_entities'));
		$db->query(NULL, 'DROP TABLE '.$this->db->quote_table('datalog_test_entity_types'));
	}

	/**
	 * @test
	 */
	public function Create_new_simple_record()
	{
		$table_name = $this->db->table_prefix().'datalog_test_entities';
		$test_entity = ORM::factory('TestEntity');
		$test_entity->description = "Testing one two";
		$test_entity->save();

		$actual = $this->_get_datalog($table_name);
		$expected = array(
			array(
				'table_name' => $table_name,
				'column_name' => 'id',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => '1',
			),
			array(
				'table_name' => $table_name,
				'column_name' => 'description',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => 'Testing one two',
			),
		);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @test
	 */
	public function Create_and_edit_simple_record()
	{
		$table_name = $this->db->table_prefix().'datalog_test_entities';
		$test_entity = ORM::factory('TestEntity');
		$test_entity->description = "Testing one two";
		$test_entity->save();
		$test_entity->description = "New description";
		$test_entity->save();

		$actual = $this->_get_datalog($table_name);
		$expected = array(
			array(
				'table_name' => $table_name,
				'column_name' => 'id',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => '1',
			),
			array(
				'table_name' => $table_name,
				'column_name' => 'description',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => 'Testing one two',
			),
			array(
				'table_name' => $table_name,
				'column_name' => 'description',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => 'Testing one two',
				'new_value' => 'New description',
			),
		);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Create a new record that references a foreign table.
	 *
	 * @test
	 */
	public function Create_new_referencing_record()
	{
		$table_name = $this->db->table_prefix().'datalog_test_entities';

		// Create records
		$entity_type = ORM::factory('TestEntityType');
		$entity_type->name = 'Test type';
		$entity_type->save();
		$entity = ORM::factory('TestEntity');
		$entity->description = "Testing one two";
		$entity->type_id = $entity_type->id;
		$entity->save();
		$this->assertEquals('Test type', $entity->type->name);

		// Check the data log
		$actual = $this->_get_datalog($table_name);
		$expected = array(
			array(
				'table_name' => $table_name,
				'column_name' => 'id',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => '1',
			),
			array(
				'table_name' => $table_name,
				'column_name' => 'type_id',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => 'Test type',
			),
			array(
				'table_name' => $table_name,
				'column_name' => 'description',
				'row_pk' => '1',
				'username' => __(Kohana::message('datalog', 'anon_username')),
				'old_value' => NULL,
				'new_value' => 'Testing one two',
			),
		);
		$this->assertEquals($expected, $actual);
	}

	private function _get_datalog($table_name)
	{
		$datalog = ORM::factory('Datalog')
			->where('table_name', '=', $table_name)
			->find_all();
		$out = array();
		foreach ($datalog as $dl)
		{
			$out[] = array(
				'table_name' => $dl->table_name,
				'column_name' => $dl->column_name,
				'row_pk' => $dl->row_pk,
				'username' => $dl->username,
				'old_value' => $dl->old_value,
				'new_value' => $dl->new_value,
			);
		}
		return $out;
	}

}

class Model_TestEntity extends ORM {

	protected $_table_name = 'datalog_test_entities';

	protected $_belongs_to = array(
		'type' => array('model'=>'TestEntityType', 'foreign_key'=>'type_id'),
	);

	protected $_has_one = array(
		'info' => array('model'=>'TestEntityInfo', 'foreign_key'=>'entity_id'),
	);

	public function candidate_key()
	{
		return $this->id.' '.$this->description;
	}

	public function save(Validation $validation = NULL)
	{
		$datalog = new DataLog($this->_table_name, $this->_original_values);
		$parent = parent::save($validation);
		$datalog->save($this->pk(), $this->_object, $this->_belongs_to);
		return $parent;
	}

}

class Model_TestEntityInfo extends ORM {

	protected $_table_name = 'datalog_test_entity_info';

	protected $_belongs_to = array(
		'entity' => array('model'=>'TestEntity', 'foreign_key'=>'entity_id'),
	);

}

class Model_TestEntityType extends ORM {

	protected $_table_name = 'datalog_test_entity_types';

	public function candidate_key()
	{
		return $this->name;
	}

}
