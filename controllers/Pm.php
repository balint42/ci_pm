<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @mainpage
 * @section Introduction
 * This is a simple boilerplate for a CodeIgniter private messaging system.
 * It comes with the following functionality:
 * \li Send messages to multiple users
 * \li Reply, delete, restore messages
 * \li Browse messages by status: deleted, unread, not deleted, sent, ...
 * \li AJAX ready function for auto-completing recipient names
 * \li ORM like base classes to convert MySQL types to PHP types
 * \li Sample views to demonstrate usage
 * \li Database structure and sample content
 * 
 * It is written according to the CI coding guides, but it does not support
 * database prefixes.
 *
 * @section Installation
 * Grab a fresh CodeIgniter installation and connect it to a MySQL database.
 * Download all ci_pm files and extract them to your "application" CI folder.
 * Be sure to overwrite the "constants.php" file! As next step open the 
 * "db.sql" file in the "application" folder and execute its contents
 * in a MySQL db. Delete the file afterwards.
 * Now you should be able to reach the module via ".../index.php/pm".
 *
 * @section Usage
 *
 * To test the system surf to ".../index.php/pm" on your server. To test the
 * auto-completing of recipient names enter only "Foo" to the recipient
 * field and click "send".
 * 
 * To use the private messaging system with your own application you will
 * want to extend the {@link User_model} with your own user authentication
 * system. Therefore you have to replace the "current_id" method in
 * {@link User_model} with your own method returning the id of the currently
 * logged in user. {@link Pm_model} uses "current_id" to get the user id
 * of the current user.
 *
 * As next step you will want to replace the views and implement e.g. AJAX calls
 * to auto-complete recipient names or show more of the backend messages to the user.
 * Also you might want to delete the sample contents from the database and implement
 * your own routing.
 */

/**
 * @brief     Pm Controller
 * @details
 * Some methods in this controller will set a flashdata status message
 * to be used by the views they load. (e.g. {@link messages})
 * Most methods also pass variables to the views they load.
 * All output passed on to the views is documented in each controller
 * method description.
 * This controller does not care what user the actions are performed for:
 * that is entirely determined in the model class.
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
class Pm extends CI_Controller {
	/**
	 * @var string: URI of home to redirect to uppon many occasions
	 */
	public $base_uri = '/pm/';
	/**
	 * @var object: global CI instance that contains e.g. the db object
	 */
	private $ci;

	/**
	 * @brief PM constructor
	 *
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		$this->ci = & get_instance();
		$this->config->load('form_validation', TRUE);
		$this->config->load('pm', TRUE);
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->model('Pm_model', 'pm_model');
		$this->load->model('User_model', 'user_model');
		$this->lang->load('pm', 'english');
	}

	/**
	 * @brief initialize
	 *
	 * Initializes values for this class.
	 *
	 * @param dateformat string: format to display dates in
	 * @return void
	 */
	public function initialize($dateformat = "Y.m.d - H:i:s")
	{
		$this->pm_model->initialize($dateformat);
	}

	/**
	 * @brief CI index function
	 *
	 * CI index function called if no other is specified
	 *
	 * @return void
	 */
	function index()
	{
		$this->messages();
	}

	/**
	 * @brief Show a specific message
	 *
	 * Show a specific message by message id.
	 * Views loaded: menu, details.
	 * Data for 'details' view: $message.
	 *
	 * @param msg_id integer: id of the message to get
	 * @return void
	 */
	function message($msg_id)
	{
		if( ! $msg_id) return;

		// Get message and flag it as read
		$message = $this->pm_model->get_message($msg_id);
		if($message)
		{
			$message = reset($message);
			// Flag message as read
			$this->pm_model->flag_read($msg_id);

			// Get recipients & get usernames instead of user ids for recipients and author
			$message[TF_PM_AUTHOR] = $this->user_model->get_username($message[TF_PM_AUTHOR]);
			$message[PM_RECIPIENTS] = $this->pm_model->get_recipients($message[TF_PM_ID]);
			$i = 0;
			foreach ($message[PM_RECIPIENTS] as $recipient)
			{
				$id = $recipient[TF_PMTO_RECIPIENT];
				$message[PM_RECIPIENTS][$i] = $this->user_model->get_username($id);
				$i++;
			}
			$data['message'] = $message;
		}
		else $data['message'] = array();

		$this->load->view('menu');
		$this->load->view('details', $data);
	}

	/**
	 * @brief Show messages
	 *
	 * Show messages.
	 * Views loaded: menu, list.
	 * Data for 'list' view: $messages (associative array|array|string).
	 *
	 * @param type integer: message type to get.
	 * Use one of the following:
	 * MSG_NONDELETED: received by user, not deleted;
	 * MSG_DELETED: received by user, deleted;
	 * MSG_UNREAD: received by user, not deleted, not read;
	 * MSG_SENT: sent by user (no trashbin, i.e. no deleted state);
	 * $type < 0: get ALL messages, deleted or not, sent to or by this user;
	 * @return void
	 */
	function messages($type = MSG_NONDELETED)
	{
		// Get & pass to view the messages view type (e.g. MSG_SENT)
		$data['type'] = $type;
		$messages = $this->pm_model->get_messages($type);

		if($messages)
		{
			// Get recipients & get usernames instead of user ids
			// for recipients and author & render message body correctly
			$i = 0;
			foreach ($messages as $message)
			{
				$messages[$i][TF_PM_BODY] = $this->render($messages[$i][TF_PM_BODY]);
				$messages[$i][TF_PM_AUTHOR] = $this->user_model->get_username($message[TF_PM_AUTHOR]);
				$messages[$i][PM_RECIPIENTS] = $this->pm_model->get_recipients($messages[$i][TF_PM_ID]);
				$j = 0;
				foreach ($messages[$i][PM_RECIPIENTS] as $recipient)
				{
					$id = $recipient[TF_PMTO_RECIPIENT];
					$messages[$i][PM_RECIPIENTS][$j] = $this->user_model->get_username($id);
					$j++;
				}
				$i++;
			}
			$data['messages'] = $messages;
		}
		else $data['messages'] = array();

		$this->load->view('menu');
		$this->load->view('list', $data);
	}

	/**
	 * @brief Send message
	 *
	 * Send a new message to the users given by username,
	 * with the given subject and given text body.
	 * Views loaded: menu, compose.
	 * Data for 'compose' view:
	 * $found_recipients (bool), $suggestions (array|string),
	 * $status (string), $message (associative array|string).
	 * Flashdata for 'compose' view: 'status'.
	 *
	 * @param recipients array|string: array with usernames
	 * @param subject string: message subject
	 * @param body string: message text
	 * @return void
	 */
	function send($recipients = NULL, $subject = NULL, $body = NULL)
	{
		$rules = $this->config->item('pm_form', 'form_validation');
		$this->form_validation->set_rules($rules);

		$data['found_recipients'] = TRUE; // assume we'll find recipients - set to FALSE below otherwise
		$data['suggestions'] = array(); // if recipients not found by exact search, save suggestions here
		$message = array();
		// Set default vals passed via parameters
		$message[PM_RECIPIENTS] = $recipients;
		$message[TF_PM_SUBJECT] = $subject;
		$message[TF_PM_BODY] 	= $body;

		if($this->form_validation->run())
		{
			// Overwrite default vals from params if form validated with vals from POST
			$message[PM_RECIPIENTS] = $this->input->post(PM_RECIPIENTS, TRUE);
			$message[TF_PM_SUBJECT] = $this->input->post(TF_PM_SUBJECT, TRUE);
			$message[TF_PM_BODY] 	= $this->input->post(TF_PM_BODY, TRUE);
			// Lets operate on copies of POST input to preserve orig vals in case of failure
			$recipients = explode(";", $this->input->post(PM_RECIPIENTS, TRUE));
			$subject = $this->input->post(TF_PM_SUBJECT, TRUE);
			$body = $this->input->post(TF_PM_BODY, TRUE);

			$recipient_ids = array();
			// Get user ids of recipients - if not found, get usernames of suggestions
			foreach ($recipients as $recipient)
			{
				$result = $this->user_model->get_userids(trim($recipient));
				array_push($recipient_ids, reset($result));
				// Try non-exact search if none found to have suggestions - in this case $data['suggestions']
				// will contain an array with original strings as keys & arrays with suggestions as values.
				if( ! reset($result))
				{
					$data['found_recipients'] = FALSE;
					$suggestions = $this->user_model->get_userids(trim($recipient), FALSE);
					if($suggestions)
						foreach ($suggestions as $suggestion)
							$data['suggestions'][$recipient] = $this->user_model->get_username($suggestion);
				}
			}

			if($data['found_recipients'])
			{
				if($this->pm_model->send_message($recipient_ids, $subject, $body))
				{
					// On success: redirect to list view of messages
					$this->session->set_flashdata('status', $this->lang->line('msg_sent'));
					redirect($this->base_uri.'messages/'.MSG_NONDELETED);
				}
				else
				{
					$status = $this->lang->line('msg_not_sent');
					$this->session->set_flashdata('status', $status);
					redirect($this->base_uri.'send/'.
							 $message[PM_RECIPIENTS].'/'.
							 $message[TF_PM_SUBJECT].'/'.
							 $message[TF_PM_BODY]);
				}
			}
			else $data['status'] = $this->lang->line('recipients_not_found');
		}

		// Only happens if sending msg unsuccessful above
		if(isset($status))
		{
			$data['status'] = $status;
			$this->session->set_flashdata('status', $status);
		}
		$data['message'] = $message;
		$this->load->view('menu');
		$this->load->view('compose', $data);
	}

	/**
	 * @brief Delete message
	 *
	 * Delete a message from inbox or sent-folder (move to trash). If 3rd parameter
	 * "redirect" is TRUE, redirect to the view specified by 2nd parameter "type".
	 * Usually this will be the same view the user deleted the message from.
	 * Views loaded: - (no view loaded since redirect).
	 * Flashdata for view redirected to: 'status'.
	 *
	 * @param msg_id integer: message to delete by msg id.
	 * @param type integer: messages view type to redirect to, e.g. MSG_SENT {@link messages}.
	 * @param redirect bool: indicating whether to redirect to a view after msg deleted.
	 * @return void
	 */
	function delete($msg_id, $type = MSG_NONDELETED, $redirect = TRUE)
	{
		if($msg_id >= 0)
			if($this->pm_model->flag_deleted($msg_id)) $status = $this->lang->line('msg_deleted');
			else $status = $this->lang->line('msg_not_deleted');
		else $status = $this->lang->line('msg_invalid_id');
		$this->session->set_flashdata('status', $status);

		// Redirect to $type (e.g. MSG_NONDELETED) view of messages
		if($redirect) redirect($this->base_uri.'messages/'.$type);
		else $this->session->keep_flashdata('status');
	}

	/**
	 * @brief Restore message
	 *
	 * Restore a message from trash: move to inbox or sent-folder, depending
	 * on where it was deleted from. The method determines which is correct.
	 * If 2nd parameter "redirect" is TRUE, redirect to trash view.
	 * Views loaded: - (no view loaded since redirect).
	 * Flashdata for view redirected to: 'status'.
	 *
	 * @param msg_id integer: message to restore by msg id.
	 * @param redirect bool: indicating whether to redirect to a view after msg deleted.
	 * @return void
	 */
	function restore($msg_id, $redirect = TRUE)
	{
		if($msg_id >= 0)
			if($this->pm_model->flag_undeleted($msg_id)) $status = $this->lang->line('msg_restored');
			else $status = $this->lang->line('msg_not_restored');
		else $status = $this->lang->line('msg_invalid_id');
		$this->session->set_flashdata('status', $status);

		// Redirect to trash bin view of messages
		if($redirect) redirect($this->base_uri.'messages/'.MSG_DELETED);
		else $this->session->keep_flashdata('status');
	}

	/**
	 * @brief Render message text
	 *
	 * Render the message body text.
	 *
	 * @param message_body string: text of the message body to be rendered
	 * @return string
	 */
	function render($message_body)
	{
		$message_body = strip_tags($message_body, '');
		$message_body = stripslashes($message_body);
		$message_body = nl2br($message_body);
		return $message_body;
	}
}

/* End of file Pm.php */