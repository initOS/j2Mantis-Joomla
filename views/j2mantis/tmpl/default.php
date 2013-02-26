<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php
JHTML::stylesheet('j2mantis.css', 'components/com_j2mantis/assets/');
JHTML::_('behavior.tooltip');
$timezone_offset = -date("H", strtotime("Y-m-d", time()))+1;
$fmt_date_long = "Y/m/d H:i";
$fmt_date_short = "d M";
?>
<div class="item-list<?php echo $this->moduleclass_sfx ?>" xmlns="http://www.w3.org/1999/html">
    <h1><?php echo $this->caption; ?></h1>
    <table id="mt_overview">
        <thead>
        <tr>
            <th>
				<?php echo JText::_('Status');?>
            </th>
            <th>
				<?php echo JText::_('Summary');?>
            </th>
			<?php if ($this->hasactionholders) { ?>
            <th>
				<?php echo JText::_('actionholder');?>
            </th>
			<?php } ?>
			<?php if ($this->hasduedate) { ?>
            <th>
				<?php echo JText::_('due date');?>
            </th>
			<?php } ?>
            <th>
				<?php echo JText::_('last update');?>
            </th>
        </tr>
        </thead>
		<?php
		foreach ($this->bugs as $bug) {
			$uxts_last_updated 		= strtotime($bug->last_updated . " +" . $timezone_offset . " hours");
			$last_updated_long 		= date($fmt_date_long, $uxts_last_updated);
			$last_updated_short 	= date($fmt_date_short, $uxts_last_updated);
			$uxts_date_submitted 	= strtotime($bug->date_submitted . " +" . $timezone_offset . " hours");
			$date_submitted_long 	= date($fmt_date_long, $uxts_date_submitted);
			$duedate_long = " ";
			$duedate_short = " ";
			if ($bug->due_date) {
				$uxts_duedate = strtotime($bug->due_date . " +" . $timezone_offset . " hours");
				$duedate_long = date($fmt_date_long, $uxts_duedate);
				$duedate_short = date($fmt_date_short, $uxts_duedate);
			}
			$url = "?option=com_j2mantis&amp;view=showbug&amp;bugid=" .
				base64_encode($this->mantis->encode((string)$bug->id)) .
				"&amp;Itemid=" .
				JRequest::getInt('Itemid', 0);
			?>
            <tr>
                <td> <?php
					echo JHTML::tooltip($bug->category . ' (' . $bug->priority->name . ')</br>' .
							'<b>S:</b> ' . $date_submitted_long . '</br>' .
							'<b>U:</b> ' . $last_updated_long
						, $bug->project->name, '', $bug->status->name);
					?>    </td>
                <td> <?php
					echo JHTML::tooltip($bug->description . '</br>'
						, $bug->project->name, '', $bug->summary, $url);
					?>    </td>
				<?php if ($this->hasactionholders) { ?>
                <td> <?php
					if ($bug->j2m['actionholderid']) {
						echo JHTML::tooltip(sprintf("%s (%s)", $bug->j2m['actionholder'], $bug->j2m['actionholderid'])
							, 'action holder', '', $bug->j2m['actionholder']);
					}
					?>  </td>
				<?php } ?>
				<?php if ($this->hasduedate) { ?>
                <td> <?php
					echo JHTML::tooltip($duedate_long
						, 'due date', '', $duedate_short)
					?>  </td>
				<?php } ?>
                <td> <?php
					echo JHTML::tooltip($last_updated_long
						, 'last update', '', $last_updated_short)
					?>  </td>
            </tr>
			<?php } ?>
    </table>
    </br>
    <a href="?option=com_j2mantis&amp;view=addbug&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"><?php echo JText::_('Add new bug report');?></a>
    </br>
    </br>
    <h3>Filter list..</h3>
    <form method="post"
          action="?option=com_j2mantis&amp;task=display&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"
          enctype="multipart/form-data">
        <select name="allowed_state[]" multiple="multiple" STYLE="width: 200px; margin:0;">
            <option <?php if ( in_array(10,$this->allowed_states) ) { echo "selected"; } ?>  value="10">new</option>
            <option <?php if ( in_array(50,$this->allowed_states) ) { echo "selected"; } ?>  value="50">assigned</option>
            <option <?php if ( in_array(80,$this->allowed_states) ) { echo "selected"; } ?>  value="80">resolved</option>
            <option <?php if ( in_array(90,$this->allowed_states) ) { echo "selected"; } ?>  value="90">closed</option>
            <option <?php if ( in_array(20,$this->allowed_states) ) { echo "selected"; } ?>  value="20">feedback</option>
            <option <?php if ( in_array(30,$this->allowed_states) ) { echo "selected"; } ?>  value="30">acknowledged</option>
            <option <?php if ( in_array(40,$this->allowed_states) ) { echo "selected"; } ?>  value="40">confirmed</option>
        </select>
        <select name="allowed_users[]" multiple="multiple" STYLE="width: 200px; margin:0;">
            <option <?php if (( in_array(0,$this->allowed_users) ) || (is_null($this->allowed_users)) ) { echo "selected"; } ?> value=0>- no action holder -</option>
			<?php foreach ($this->actionholders as $actionholder) { ?>
            <option <?php if (( in_array($actionholder->id,$this->allowed_users) ) || (is_null($this->allowed_users))){ echo "selected"; } ?>
                    value="<?php echo $actionholder->id; ?>"><?php echo $actionholder->name; ?></option>
			<?php } ?>
        </select>
        <input style="background-color: lightsteelblue;" type="submit"
               value="<?php echo JText::_('submit');?>">
    </form>
</div>
