<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
 * @brief     Base Model
 * @details
 * The model can be used by all higher models and contains basic tools
 * for the db.
 *
 * Copyright (c) 2015
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * @author    Balint Morvai
 * @version   0.9
 * @copyright MIT License
 */
class Base_model {
	/**
	 * @var object: global CI instance that contains e.g. the db object
	 */
	private $ci;

	/**
	 * @brief __construct
	 *
	 * Inits vars needed by class.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// get ci reference
		$this->ci = & get_instance();
	}

	/**
	 * @brief convert_mysql_type
	 *
	 * Function to convert a mysql field type specification string to a
	 * local field type TFIELD_*.
	 *
	 * @param field_type string: field type specification string
	 * @param field_max_length int: maximum field length
	 * @return int local field type TFIELD_*
	 */
	public function convert_mysql_type($field_type, $field_max_length = -1)
	{
		$field_type = strtolower($field_type);
		if($tmp_pos = strpos($field_type, '(')) $field_type = substr($field_type, 0, $tmp_pos);
		if($tmp_pos = strpos($field_type, '[')) $field_type = substr($field_type, 0, $tmp_pos);
		switch($field_type)
		{
			case 'bit':
				$type = TFIELD_BOOL;
				break;
			case 'tinyint':
				if($field_max_length == 1)	$type = TFIELD_BOOL;
				else $type = TFIELD_INT;
				break;
			case 'smallint':
			case 'mediumint':
			case 'int':
				$type = TFIELD_INT;
				break;
			case 'bigint':
			case 'float':
			case 'double':
			case 'decimal':
				$type = TFIELD_FLOAT;
				break;
			case 'char':
			case 'varchar':
			case 'tinytext':
			case 'text':
			case 'mediumtext':
			case 'longtext':
			case 'binary':
			case 'varbinary':
			case 'tinyblob':
			case 'mediumblob':
			case 'blob':
			case 'longblob':
			case 'enum':
			case 'set':
				$type = TFIELD_STR;
				break;
			case 'date':
			case 'datetime':
			case 'time':
			case 'timestamp':
			case 'year':
				$type = TFIELD_DATE;
				break;
			default:
				$type = TFIELD_DEFAULT;
				break;
		}

		return $type;
	}

	/**
	 * @brief is_function_defined
	 *
	 * Function checking whether the (mysql) db contains the given costum
	 * function definition.
	 *
	 * @param function_name string: name of the function to be checked
	 * @return bool
	 */
	public function is_function_defined($function_name)
	{
		$passed = TRUE;

		$this->db->select('ROUTINE_NAME');
		$this->db->from('INFORMATION_SCHEMA.ROUTINES');
		$this->db->where("`ROUTINE_TYPE` = 'FUNCTION' AND
						  `ROUTINE_SCHEMA` = '".$this->db->database."' AND
						  `ROUTINE_NAME` = '$function_name'");
		$query = $this->db->get();
		if($query->num_rows() == 0)
			$passed = FALSE;

		return $passed;
	}
}

/* End of file Base_model.php */
