<?php if(count($messages)>0):?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td width="20%" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		<?php if($type != MSG_SENT) echo 'From'; else echo 'Recipients'; ?>
	</td>
    <td width="40%" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Subject
	</td>
    <td width="20%" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Date
	</td>
    <?php if($type != MSG_SENT): ?>
	<td width="10%" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		Reply
	</td>
	<?php endif; ?>
    <td width="10%" align="center" style="font-weight:bold; background:#F2F2F2; padding:4px;">
		<?php if($type != MSG_DELETED) echo 'Delete'; else echo 'Restore'; ?>
	</td>
    </tr>

	<?php for ($i=0; $i<count($messages); $i++): ?>
	<tr style="background:#FCFBF3;">
		<td style="padding:4px;">
			<?php
				if($type != MSG_SENT) echo $messages[$i][TF_PM_AUTHOR];
				else
				{
				  	$recipients = $messages[$i][PM_RECIPIENTS];
					foreach ($recipients as $recipient)
						echo (next($recipients)) ? $recipient.', ' : $recipient;
				}?>
		</td>
		<td style="padding:4px;">
			<a href='<?php echo site_url().'/pm/message/'.$messages[$i][TF_PM_ID]; ?>'><?php echo $messages[$i][TF_PM_SUBJECT] ?></a>
		</td>
		<td style="padding:4px;">
			<?php echo $messages[$i][TF_PM_DATE]; ?>
		</td>
	    <?php if($type != MSG_SENT): ?>
		<td style="padding:4px;">
			<?php echo '<a href="'.site_url().'/pm/send/'.$messages[$i][TF_PM_AUTHOR].'/RE&#58;'.$messages[$i][TF_PM_SUBJECT].'"> reply </a>' ?>
		</td>
		<?php endif; ?>
		<td style="padding:4px;" align="center">
			<?php if($type != MSG_DELETED)
					echo '<a href="'.site_url().'/pm/delete/'.$messages[$i][TF_PM_ID].'/'.$type.'"> x </a>';
				  else
					echo '<a href="'.site_url().'/pm/restore/'.$messages[$i][TF_PM_ID].'"> o </a>'; ?>
		</td>
	</tr>
	<?php endfor;?>
</table>
<?php else:?>
<h1>No messages found.</h1>
<?php endif;?>
