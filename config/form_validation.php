<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['pm_form'] = array(
	array(
		'field' => PM_RECIPIENTS,
		'label' => 'Recipients',
		'rules' => 'required'
	),
	array(
		'field' => TF_PM_SUBJECT,
		'label' => 'Subject',
		'rules' => 'required'
	),
	array(
		'field' => TF_PM_BODY,
		'label' => 'Message Text',
		'rules' => 'required'
	)
);

/* End of file form_validation.php */
