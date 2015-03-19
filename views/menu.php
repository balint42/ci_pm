<?php $this->load->helper('url'); ?>
<table width="100%" border="1" style="border-collapse:collapse; border-color:#CCCCCC;" cellpadding="4" cellspacing="4">
	<tr>
    <td style="padding:10px;">
		<a href="<?php echo site_url()."/pm"?>">Inbox</a> &nbsp;&nbsp;&nbsp;
		<a href="<?php echo site_url()."/pm/messages/".MSG_UNREAD?>">Unread</a> &nbsp;&nbsp;&nbsp; 
		<a href="<?php echo site_url()."/pm/messages/".MSG_SENT?>">Sent</a> &nbsp;&nbsp;&nbsp;
		<a href="<?php echo site_url()."/pm/messages/".MSG_DELETED?>">Trashed</a> &nbsp;&nbsp;&nbsp;
		<a href="<?php echo site_url()."/pm/send"?>">Compose</a>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	</tr>
</table>
<br />