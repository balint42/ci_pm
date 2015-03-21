<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief     Table Model
 * @details
 * The model can be used by all higher models and contains basic info
 * on the tables used and it writes and reads all tables and contains
 * functions therefore. Results are stored in the "results" array and
 * structured as described by the "*_fields" and "*_types" arrays.
 * Since the table name can only be set upon creation, class instances
 * are unrevocably linked to the given table, so is the reference to
 * the db instance of the model it works for.
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
class Table_model {
	/**
	 * @var bool: setting whether to enforce the field vars in the result
	 * to their correct type (by typecast) or to leave them string only
	 * as returned by CI by default
	 */
	protected $enforce_field_types = TRUE;
	/**
	 * @var string: date format string for date types
	 */
	protected $dateformat = "Y.m.d - H:i:s";
	/**
	 * @var object: global CI instance that contains e.g. the db object
	 */
	private $ci;
	/**
	 * @var string: name of the table
	 */
	protected $name;
	/**
	 * @var array: codeigniter db result array from the table
	 */
	public $results = array();
	/**
	 * @var int: id of last db insert to the table - if failed, set to FALSE
	 */
	public $insert_id = FALSE;
	/**
	 * @var array: associative array with the table field names as keys
	 * and field types (of local TFIELD_* type) as values
	 */
	protected $types = array();
	/**
	 * @var array: numeric array with field positions in the table as
	 * numbers and db field names as values
	 */
	protected $fields = array();

	/**
	 * @brief __construct
	 *
	 * Inits some vars needed by class, two are required: the table's
	 * name to act upon and the ci reference.
	 *
	 * @param _name string: table name of the table to act upon
	 * @param dateformat string: date format to format dates in, e.g. "d.m.Y - H:i"
	 * @param _enforce_field_types bool: setting whether to enforce field types in PHP by cast
	 * @return object
	 */
	public function __construct($_name = NULL, $dateformat = '"d.m.Y - H:i"', $_enforce_field_types = TRUE)
	{
		// set params
		if(is_array($_name)) $_name = reset($_name);
		$this->name = $_name;
		$this->enforce_field_types = $_enforce_field_types;
		$this->ci = & get_instance();
		$this->ci->load->database();

		if($this->name)
		{
			if( ! $this->ci->db->table_exists($this->name))
			{
				if($this->name) log_message('error', "db error: table not found by name '{$_name}'");
				return FALSE;
			}
			// get db field info
			$this->fields = $this->ci->db->field_data($this->name);
			// define types: convert db types to own local types
			foreach ($this->fields as $field)
			{
				$this->ci->load->library('Base_model');
				$type = $this->ci->base_model->convert_mysql_type($field->type, $field->max_length);
				$this->types[$field->name] = $type;
			}
			// define data fields
			$this->fields = array_values($this->ci->db->list_fields($this->name));
		}
	}

	/**
	 * @brief initialize
	 *
	 * Initializes values for this class.
	 * Resets the results array to maintain consistency of field types.
	 *
	 * @param _dateformat string: format to display dates in
	 * @param _enforce_field_types bool: setting whether to enforce field types in PHP by cast
	 * @return void
	 */
	public function initialize($_dateformat = "d.m.Y - H:i", $_enforce_field_types = FALSE)
	{
		// Define the date format
		$this->dateformat = $_dateformat;
		// Define whether to enforce db field types in PHP by type cast
		$this->enforce_field_types = $_enforce_field_types;
		// Since we change "enforce_field_types" reset results array
		$this->results = array();
	}

	/**
	 * @brief Check whether test var is a valid db index
	 *
	 * Function checking whether the supplied var is a valid db index.
	 * Therefore it has to be (convertible to) a positive integer or zero.
	 *
	 * @param testvar int: var to check if it is valid index number
	 * @return bool
	 */
	public function is_valid_index($testvar)
	{
		$passed = FALSE;

		// count number of correct fields in "data"
		if(ctype_digit($testvar) OR is_int($testvar))
			if((int)$testvar >= 0)
				$passed = TRUE;

		return $passed;
	}

	/**
	 * @brief Get types array
	 *
	 * Function returning the (protected) {@link types} array.
	 *
	 * @return array
	 */
	public function get_types()
	{
		return $this->types;
	}

	/**
	 * @brief Get fields array
	 *
	 * Function returning the (protected) {@link fields} array.
	 *
	 * @return array
	 */
	public function get_fields()
	{
		return $this->fields;
	}

	/**
	 * @brief Get name string
	 *
	 * Function returning the (protected) {@link name} string.
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @brief Check whether data array is valid db array
	 *
	 * Function checking whether the supplied data array is equal to the ones returned from
	 * the given table this base model is connected to. Therefore it has to be an associative
	 * array containing all the same table field names as keys as the result arrays
	 * returned by CI.
	 *
	 * @param data array: associative array with table field names as keys to be checked if it
	 * is equal to result arrays returned by CI from this model's table
	 * @return bool
	 */
	public function is_valid_data($data)
	{
		$passed = TRUE;

		if( ! $this->is_valid_subdata($data) OR (count($data) != count($this->fields)))
			$passed = FALSE;

		return $passed;
	}

	/**
	 * @brief Check whether data array is valid db array subset
	 *
	 * Function checking whether the supplied data array is a partial data from
	 * the given table this base model is connected to. Therefore it has to be an
	 * associative array containing one or more table field names as keys but no
	 * keys that are not table field names. Since it does not have to contain
	 * ALL the data fields it is only a partial- or subdata array.
	 *
	 * @param data array: associative array with table field names as keys to be checked if it
	 * is a subdata from this model's table
	 * @return bool
	 */
	public function is_valid_subdata($data)
	{
		$passed = TRUE;

		// count number of correct fields in "data"
		if( ! empty($data) AND is_array($data))
		{
			foreach ($data as $key => $value)
				if( ! in_array($key, $this->fields))
					$passed = FALSE;
		}
		else $passed = FALSE;

		return $passed;
	}

	/**
	 * @brief Get data from db
	 *
	 * Function that either executes db->get() if "query_string" is FALSE (executing the
	 * currently set query) OR executes db->query($query_string) if "query_string" is given
	 * and returns data as "result_array" as well as puts them in the {@link results} var.
	 *
	 * Use it to retrieve data (rows) from db. If you dont supply a query string set query
	 * specifics before call using CI functions like db->select, db->from, db->where,...
	 * Use the {@link get_types} function to find out about the table structure.
	 *
	 * Returns FALSE on error, array otherwise
	 *
	 * @param query_string string: query string to be executed using the CI "query" function
	 * instead of just using the CI "get()" function. When FALSE "get()" is used.
	 * @return array
	 */
	public function get_data($query_string = FALSE)
	{
		// get data depending on whether query string is given with "query" or "get"
		try
		{
			if($query_string)
				$data = $this->ci->db->query($query_string);
			else
				$data = $this->ci->db->get();
		}
		catch(Exception $e)
		{
			log_message('error', "db error: error executing query to get matching data ({$e->getMessage()})");
			return FALSE;
		}

		$data = $data->result_array();
		$this->results = $data;
		// if desired, convert each data field to its correct PHP type
		if(( ! empty($data)))
		{
			if($this->enforce_field_types)
			{
				foreach ($data as $index => $row)
				{
					foreach ($row as $field => $value)
					{
						// convert to types
						switch($this->types[$field])
						{
							case TFIELD_FLOAT:
								$this->results[$index][$field] = (float)$value;
								break;
							case TFIELD_INT:
								$this->results[$index][$field] = (int)$value;
								break;
							case TFIELD_BOOL:
								$this->results[$index][$field] = (bool)$value;
								break;
							case TFIELD_DATE:
								 $this->results[$index][$field] = date($this->dateformat, strtotime($value));
								break;
							case TFIELD_STR:
								$this->results[$index][$field] = $value;
								break;
						}
					}
				}
			}
			else // if enforce_field_types FALSE at least convert date fields
				foreach ($data as $index => $row)
					foreach ($row as $field => $value)
						if($this->types[$field] == TFIELD_DATE)
							$this->results[$index][$field] = date($this->dateformat, strtotime($value));
		}

		return $this->results;
	}


	/**
	 * @brief Complete sub-data array with data from db
	 *
	 * Function that either executes db->get() if "query_string" is FALSE (executing the
	 * currently set query) OR executes db->query($query_string) if "query_string" is set.
	 * The "query_string" or preset query should select exactly one table row, the one to
	 * be used for completion of the given subdata array. If more than one row is returned,
	 * only the first row is used.
	 * The function gets the complete result array of the selected row from the table
	 * and completes the given data array with its values and keys: it inserts (only)
	 * missing keys with its values.
	 * The "data" param has to be a valid subdata array and it is checked therefore.
	 * Returns array if successful and empty array if no results and FALSE on error.
	 *
	 * Use the {@link get_types} function to build a valid data array easily.
	 *
	 * @param subdata array: associative array with db field names as keys and values as values
	 * as data in {@link results} var. Checked if valid subdata.
	 * @param query_string string: query string to be executed using the CI "query" function
	 * instead of just using the CI "get()" function. When FALSE "get()" is used.
	 * @return array
	 */
	public function complete_data($subdata, $query_string = FALSE)
	{
		// check if given data array is a valid (partial) data
		if( ! $this->is_valid_subdata($subdata))
		{
			log_message('error', 'error: data array not valid)');
			return FALSE;
		}

		try
		{
			if($query_string)
				$data = $this->ci->db->query($query_string);
			else
				$data = $this->ci->db->get();
		}
		catch(Exception $e)
		{
			log_message('error', "db error: error executing query to get matching data ({$e->getMessage()})");
			return FALSE;
		}

		$data = $data->result_array();

		// if desired, convert each data field to its correct PHP type
		if( ! empty($data))
		{
			$data = reset($data);
			if($this->enforce_field_types)
			{
				foreach ($this->types as $field => $type)
				{
					// convert to types
					switch($type)
					{
						case TFIELD_FLOAT:
							$data[$field] = (float)$data[$field];
							break;
						case TFIELD_INT:
							$data[$field] = (int)$data[$field];
							break;
						case TFIELD_BOOL:
							$data[$field] = (bool)$data[$field];
							break;
						case TFIELD_DATE:
							$data[$field] = date($this->dateformat, strtotime($data[$field]));
							break;
						case TFIELD_STR:
							$data[$field] = $data[$field];
							break;
					}
				}
			}
		}
		else
		{
			log_message('error', "db error: getting row from db for completion of given subdata failed");
			return array();
		}

		// complete given data array with table data array
		foreach ($data as $field => $value)
			if( ! array_key_exists($field, $subdata))
				$subdata[$field] = $value;

		return $subdata;
	}

	/**
	 * @brief Insert data to db
	 *
	 * Function that executes db->insert($this->name) either without data array if "data" param
	 * is FALSE (executing the (pre)set query) OR executes db->insert($this->name, $data)
	 * if "data" param is set. If handed over, the "data" param has to be a valid subdata
	 * array and it is checked therefore. Fields missing have to have default values in the db.
	 * Also sets {@link insert_id}, sets it to FALSE on failure. Returns TRUE if insert
	 * successful and FALSE otherwise.
	 *
	 * Use the {@link get_types} function to build a valid data array easily.
	 *
	 * @param data array: associative array with db field names as keys and values as values
	 * as data in {@link results} var. Checked if valid subdata.
	 * @return bool
	 */
	public function insert_data($data = FALSE)
	{
		// check if given data array is a valid (partial) data
		if($data)
		{
			if( ! $this->is_valid_subdata($data))
			{
				log_message('error', 'error: data array not valid)');
				return FALSE;
			}
		}

		try
		{
			if($data)
				$this->ci->db->insert($this->name, $data);
			else
				$this->ci->db->insert($this->name);

			$this->insert_id = $this->ci->db->insert_id();
		}
		catch(Exception $e)
		{
			log_message('error', "db error: failed to insert data ({$e->getMessage()})");
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @brief Update data in db
	 *
	 * Function that executes db->update($this->name) either without data array if "data" param
	 * is FALSE (executing the (pre)set query) OR executes db->update($this->name, $data)
	 * if "data" param is set. If handed over, the "data" param has to be a valid subdata
	 * array and it is checked therefore. NOTE: in both cases a correct "db->where" query
	 * HAS TO BE SET BEFORE CALLING! The row to update is specified by this where query
	 * and NOT by the "data" array!
	 * Returns TRUE if update successful and FALSE if otherwise.
	 *
	 * Use the {@link get_types} function to build a valid data array easily.
	 *
	 * @param data array: associative array with db field names as keys and values as values
	 *				as data in {@link results} var. Checked if valid subdata.
	 * @return bool
	 */
	public function update_data($data = FALSE)
	{
		// check if given data array is a valid (partial) data
		if($data)
		{
			if( ! $this->is_valid_subdata($data))
			{
				log_message('error', 'error: data array not valid)');
				return FALSE;
			}
		}

		try
		{
			if($data)
				$this->ci->db->update($this->name, $data);
			else
				$this->ci->db->update($this->name);
		}
		catch(Exception $e)
		{
			log_message('error', "db error: failed to update data ({$e->getMessage()}), ".
						'did you forget to set a correct where query before call?');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @brief Delete data in db
	 *
	 * Function that executes db->delete($this->name) either without id array if "ids" param
	 * is FALSE (executing the (pre)set query) OR executes db->delete($this->name, $ids)
	 * if "ids" param is set. If handed over, the "ids" param has to be a valid subdata
	 * array and it is checked therefore - more specifically it'll mostly contain only
	 * the ids to delete: array('id_field_name' => $id1, 'id_field_name' => $id2, ...)
	 *
	 * Returns TRUE if delete successful and FALSE if otherwise.
	 *
	 * Use the {@link get_types} function to build a valid data array easily.
	 *
	 * @param ids array: associative array with db field names as keys and values as values
	 * as data in {@link results} var. Checked if valid subdata.
	 * @return bool
	 */
	public function delete_data($ids = FALSE)
	{
		// check if given ids array is a valid (partial) data array
		if($ids)
		{
			if( ! $this->is_valid_subdata($ids))
			{
				log_message('error', 'error: ids array not valid)');
				return FALSE;
			}
		}

		try
		{
			if($ids)
				$this->ci->db->delete($this->name, $ids);
			else
				$this->ci->db->delete($this->name);
		}
		catch(Exception $e)
		{
			log_message('error', "db error: failed to delete data ({$e->getMessage()})");
			return FALSE;
		}

		return TRUE;
	}
}

/* End of file Table_model.php */
