<?php
/*
 * Note:
 * 'name' is the "name" property of the input field / list
 * 'id' is the "id" property of the input field / list AND the "for" property of the label field
 * 'name' and 'id' dont have to be the same - but it is most logical
 */
$this->load->library('session');
$MAX_INPUT_LENGTHS = $this->config->item('$MAX_INPUT_LENGTHS', 'pm');
$recipients = array(
	'name'	=> PM_RECIPIENTS,
	'id'	=> PM_RECIPIENTS,
	'value' => set_value(PM_RECIPIENTS, $message[PM_RECIPIENTS]),
	'maxlength'	=> $MAX_INPUT_LENGTHS[PM_RECIPIENTS], 
	'size'	=> 40,
);
$subject = array(
	'name'	=> TF_PM_SUBJECT,
	'id'	=> TF_PM_SUBJECT,
	'value' => set_value(TF_PM_SUBJECT, $message[TF_PM_SUBJECT]),
	'maxlength'	=> $MAX_INPUT_LENGTHS[TF_PM_SUBJECT], 
	'size'	=> 40
);
$body = array(
	'name'	=> TF_PM_BODY,
	'id'	=> TF_PM_BODY,
	'value' => set_value(TF_PM_BODY, $message[TF_PM_BODY]),
	'cols'	=> 80,
	'rows'	=> 5
);
?>

<?php echo form_open($this->uri->uri_string()); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="5%"><?php echo form_label('To', $recipients['id']); ?></td>
		<td width="33%"><?php echo form_input($recipients); ?></td>
		<td><?php echo form_error($recipients['name']); ?></td>	
	</tr>	
	<tr>
		<td><?php echo form_label('Subject', $subject['id']); ?></td>
		<td><?php echo form_input($subject); ?></td>
		<td><?php echo form_error($subject['name']); ?></td>	
	</tr>	
	<tr>
		<td><?php echo form_label('Message', $body['id']); ?></td>
		<td><?php echo form_textarea($body); ?></td>
		<td><?php echo form_error($body['name']); ?></td>	
	</tr>
	<tr>
	<td colspan=2 align="center" valign="top" style="background:#F2F2F2; padding:4px;">
		<label>
			<!-- DO NOT CHANGE BUTTON NAME, NEEDED FOR CONTROLLER "send" -->
			<input type="submit" name="btnSend" id="btnSend" value="Send" />
		</label>
	</td>
	<td></td>
	</tr>	
	<tr>
	<td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
	</td>
	<td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
	<?php
	if(isset($status)) echo $status.' ';
	if($this->session->flashdata('status')) echo $this->session->flashdata('status').' ';
	if(!$found_recipients)
	{
		foreach($suggestions as $original => $suggestion) 
		{
			echo 'Did you mean <font color="#00CC00">'.$suggestion.'</font> for <font color="#CC0000">'.$original.'</font> ?'; 
			echo '<br />';
		}
	} ?>
	</td>
	<td></td>
	</tr>
</table>
<?php echo form_close(); ?>
