<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<?php
JHTML::stylesheet('j2mantis.css', 'components/com_j2mantis/assets/');
JHTML::_('behavior.tooltip'); ?>
<h1><?php echo $this->caption; ?></h1>
<table  id="mt_overview">
<thead>
<tr>
	<th>
	  <?php echo JText::_('Status');?>
	</th>
	<th>
	  <?php echo JText::_('Summary');?>
	</th>
	<th>
	  <?php echo JText::_('last update');?>
	</th>
</tr>
</thead>
<?php
	$timezone_offset = -date("H",strtotime("Y-m-d",time()));
	foreach($this->bugs as $bug) {
		$uxts_last_updated=strtotime($bug->last_updated. " +" . $timezone_offset . " hours");
		$fmt_date_long="Y/m/d H:i";
		$fmt_date_short="y M";
		$last_updated_long 	= date( $fmt_date_long ,$uxts_last_updated);
		$last_updated_short = date($fmt_date_short ,$uxts_last_updated);
		$uxts_date_submitted=strtotime($bug->date_submitted. " +" . $timezone_offset . " hours");
		$date_submitted_long=date( $fmt_date_long ,$uxts_date_submitted);
		$url="?option=com_j2mantis&amp;view=showbug&amp;bugid=".
		     base64_encode($this->mantis->encode((string)$bug->id)).
			 "&amp;Itemid=".
			 JRequest::getInt('Itemid',0);
?>
<tr>
	<td> <?php
		echo JHTML::tooltip(  $bug->category.' ('.$bug->priority->name.')</br>'.
				              '<b>S:</b> '. $date_submitted_long. '</br>'.
				              '<b>U:</b> '. $last_updated_long
							, $bug->project->name, '', $bug->status->name);
?>	</td>
	<td> <?php
		echo JHTML::tooltip(  $bug->description.'</br>'
			, $bug->project->name, '', $bug->summary, $url);
?>	</td>
	<td> <?php
		echo JHTML::tooltip(  $last_updated_long
        	, 'last update', '', $last_updated_short)
?>  </td>
</tr>
<?php } ?>
</table>
</br>
<a href="?option=com_j2mantis&amp;view=addbug&amp;Itemid=<?php echo JRequest::getInt('Itemid',0);?>"><?php echo JText::_('Add new bug report');?></a>
