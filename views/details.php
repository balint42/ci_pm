<?php if(count($message)>0):?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Subject
	</td>
    <td align="left" valign="top" style="background:#F2F2F2; padding:4px;">
		<?php echo $message[TF_PM_SUBJECT]; ?>
	</td>
    </tr>

	<tr>
    <td width="14%" align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		From
	</td>
    <td width="86%" align="left" valign="top" style="background:#F2F2F2; padding:4px;">
		<?php echo $message[TF_PM_AUTHOR]; ?>
	</td>
    </tr>

	<tr>
    <td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		To
	</td>
    <td align="left" valign="top" style="background:#F2F2F2; padding:4px;">
		<?php foreach($message[PM_RECIPIENTS] as $recipient) echo (next($message[PM_RECIPIENTS])) ? $recipient.', ' : $recipient; ?>
	</td>
    </tr>

	<tr>
    <td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Date
	</td>
    <td align="left" valign="top" style="background:#F2F2F2; padding:4px;">
		<?php echo $message[TF_PM_DATE]; ?>
	</td>
    </tr>

	<tr>
    <td align="left" valign="top" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Message
	</td>
    <td align="left" valign="top" style="background:#F2F2F2; padding:4px;">
		<?php echo $message[TF_PM_BODY]; ?>
	</td>
    </tr>
</table>
<?php else:?>
	<h1>No message found.</h1>
<?php endif;?>
