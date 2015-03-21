<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
 * @brief     User Model
 * @details
 * The model works using one table: it contains the users.
 * The model provides functions to get info on the users.
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
class User_model extends CI_Model {
	/**
	 * @var object: table_model object that manages table1 (users)
	 */
	public $table1;

	/**
	 * @brief User model constructor
	 *
	 * @param dateformat string: format to display dates in
	 * @param enforce_field_types bool: setting whether to enforce field types in PHP by cast
	 * @return void
	 */
	function __construct($dateformat = "d.m.Y - H:i", $enforce_field_types = TRUE)
	{
		parent::__construct();
		$this->load->library('Table_model');
		$this->table1 = new table_model(TABLE_USERS, $dateformat, $enforce_field_types);
	}

	/**
	 * @brief initialize
	 *
	 * Initializes values for this class.
	 *
	 * @param dateformat string: format to display dates in
	 * @param enforce_field_types bool: setting whether to enforce field types in PHP by cast
	 * @return void
	 */
	public function initialize($dateformat = "d.m.Y - H:i", $enforce_field_types = TRUE)
	{
		// Define the date format & whether db field types are enforced in PHP by type cast
		$this->table1->initialize($dateformat, $enforce_field_types);
	}

	/**
	 * @brief Get user id from a username
	 *
	 * Get user id from a username - gets any users id.
	 * Per default performs an exact match search and returns
	 * 1 or 0 ids in enumerated array! (not CI result array)
	 * If you want a fuzzy search pass 2nd parameter FALSE. This will
	 * search for any name containing given string and return ids - the
	 * max number of returned ids (in the case of fuzzy search) is
	 * limited by the 3rd optional parameter.
	 * Returns an enumerated array in any case, containing 1 or more user
	 * ids or empty if no matches found.
	 *
	 * @param username string: username to get user id(s) for
	 * @param exact bool: if TRUE, exact search, returns 1 or 0 user ids in array;
	 * 				if FALSE, fuzzy search, return 1 or more user ids in array
	 * @param max_id_count int: max number of ids returned if fuzzy search done
	 * @return array
	 */
	public function get_userids($username, $exact = TRUE, $max_id_count = 10)
	{
		$this->db->select(TF_USER_ID);
		$this->db->from($this->table1->get_name());
		if($exact)
		{
			$this->db->where(TF_USER_NAME, $username);
			$this->db->limit(1, 0);
		}
		else
		{
			$this->db->like(TF_USER_NAME, $username);
			$this->db->limit($max_id_count, 0);
		}

		$retval = array();
		if($res = $this->table1->get_data())
			foreach($res as $row)
				array_push($retval, $row[TF_USER_ID]);

		return $retval;
	}

	/**
	 * @brief Get user name from an id.
	 *
	 * Get user name from an id - gets any users name, not just logged
	 * in users.
	 * Returns a string with the username.
	 *
	 * @param id int: user id to get user name for
	 * @return string
	 */
	public function get_username($id)
	{
		$this->db->select(TF_USER_NAME);
		$this->db->from($this->table1->get_name());
		$this->db->where(TF_USER_ID, $id);
		$this->db->limit(1, 0);

		$retval = '';
		if($res = $this->table1->get_data())
			$retval = $res[0][TF_USER_NAME];

		return $retval;
	}

	/**
	 * @brief dummy method returning first user id found
	 *
	 * !!! DUMMY METHOD - IMPLEMENT THIS AS NEEDED !!!
	 * Get user id of current user.
	 * !!! DUMMY METHOD - IMPLEMENT THIS AS NEEDED !!!
	 *
	 * @return int
	 */
	public function current_id()
	{
		$this->db->select(TF_USER_ID);
		$this->db->from($this->table1->get_name());
		$this->db->limit(1, 0);

		$retval = -1;
		if($res = $this->table1->get_data())
			$retval = $res[0][TF_USER_ID];

		return $retval;
	}
}

/* End of file User_model.php */
