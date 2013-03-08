<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::stylesheet('j2mantis.css', 'components/com_j2mantis/assets/');
$timezone_offset = -date("H", strtotime("Y-m-d", time()))+1;
$fmt_date_long = "Y/m/d H:i";
$fmt_date_short = "d M";
?>
<div class="j2m_showbug<?php echo $this->moduleclass_sfx ?>" id="j2Mantis">
    <!-- <a href="?option=com_j2mantis" style="float: right">zurück zur Übersicht</a> -->
    <h2><?php echo JText::_('Action holder & due date');?></h2>

    <form method="post"
          action="?option=com_j2mantis&amp;task=editBug&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"
          enctype="multipart/form-data">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="duedate"><?php echo JText::_('Due date');?></label>
		<?php echo JHTML::_('calendar', $this->due_date, 'due_date', 'dd_id', '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10')); ?>
        <br/>
		<?php if (isset($this->actionholders)) { ?>
        <label>Actionholder:</label>

        <select name="actionholderId" STYLE="width: 200px">
            <option
				<?php if ($this->default_autionholderid == -1) { ?>
                    selected
				<?php } ?>
                    value="-1">- none -
            </option>
			<?php foreach ($this->actionholders as $actionholder) { ?>
            <option
				<?php if ($this->default_autionholderid == $actionholder->id) { ?>
                    selected
				<?php } ?>
                    value="<?php echo $actionholder->id; ?>"><?php echo $actionholder->name; ?></option>
			<?php } ?>
        </select>
        <br/>
		<?php } ?>
        <label>Actionstatus:</label>


        <select name="actionstatus" default="<?php echo $this->bug->status->id ?>"  STYLE="width: 200px">
            <option <?php $val=10; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">new</option>
            <option <?php $val=50; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">assigned</option>
            <option <?php $val=80; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">resolved</option>
            <option <?php $val=90; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">closed</option>
            <option <?php $val=20; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">feedback</option>
            <option <?php $val=30; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">acknowledged</option>
            <option <?php $val=40; if ($val==$this->bug->status->id) { echo "selected ";} echo "value=\"".$val ?>">confirmed</option>
		</select>
        <br/>

        <input style="float:right; background-color: lightsteelblue;" type="submit"
               value="<?php echo JText::_('submit');?>">
        <br/>
    </form>
    <h2><?php echo $this->caption; ?></h2>
    <table id="mt_overview">
        <tr class="head">
            <td>
				<?php echo JText::_('Status');?>: <?php echo $this->bug->status->name ?>
            </td>
            <td>
				<?php echo $this->bug->category . " <i>" . JText::_('in') . "</i> " . $this->bug->project->name;?>
            </td>
            <td>
				<?php
					$uxts_last_updated 		= strtotime($this->bug->last_updated . " +" . $timezone_offset . " hours");
					$last_updated_long 		= date($fmt_date_long, $uxts_last_updated);
					echo $last_updated_long; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
				<?php echo $this->bug->summary ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
				<?php echo nl2br($this->bug->description) ?>
            </td>
        </tr>
		<?php if (!empty($this->additional_info_readonly)) { ?>
        <tr>
            <td colspan="3">
				<?php echo $this->additional_info_readonly;nl2br($this->additional_info_readonly); ?>
            </td>
        </tr>
		<?php }; ?>
		<?php if (count($this->bug->attachments) > 0): ?>
        <tr>
            <td><?php echo JText::_('File');?>:</td>
            <td colspan="2">

				<?php foreach ($this->bug->attachments as $att): ?>
                <p><?php echo $att->filename ?> (
                    <i><?php echo gmdate("d.m.Y H:i", strtotime($att->date_submitted)); ?></i> )</p>
				<?php endforeach; ?>
            </td>
        </tr>
		<?php endif; ?>
    </table>
    <h2><?php echo JText::_('attach File');?></h2>

    <form method="post"
          action="?option=com_j2mantis&amp;task=addFile&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"
          enctype="multipart/form-data">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="name">File:</label>
        <input type="file" name="name" size="30" id="name" maxlength="100000"/><br/>
        <input style="float:right; background-color: lightsteelblue;" type="submit"
               value="<?php echo JText::_('submit');?>">
    </form>
	<?php //var_dump($this->bug); ?>
	<?php
	if (is_array($this->bug->notes) && sizeof($this->bug->notes) > 0) {
		?>
        <br/>
        <h2><?php echo JText::_('Notes');?></h2>
        <table>
			<?php foreach ($this->bug->notes as $note) { ?>
            <tr>
                <td class="author">
					<?php echo $note->reporter->real_name; ?>
                    <br/>
                    <span>
					<?php
						$uxts_submitted 		= strtotime($note->date_submitted . " +" . $timezone_offset . " hours");
						$last_submitted 		= date($fmt_date_long, $uxts_submitted);
						echo $last_submitted; ?>
                </td>
                <td>
					<?php echo nl2br($note->text); //var_dump($note)?>
                </td>
            </tr>
			<?php } ?>
        </table>
		<?php } ?>
	<?php if ($this->bug->status->id < 90) { /* check if closed */ ?>
    <br/>
    <h2><?php echo JText::_('add Note');?></h2>
    <form method="post"
          action="?option=com_j2mantis&amp;task=addNote&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="name"><?php echo JText::_('Name'); if ($this->fo_name == 1) echo '*';?></label>
        <input type="text" name="name"
               id="name" <?php if ($this->fo_name == 1) echo 'class="required"'; if (!$this->fo_nameedit) echo ' readonly'; ?>
			<?php if (!empty($this->user->name)) {
			echo 'value="' . $this->user->name . ' [' . $this->user->username . ']"';
		} ?>  />
        <br/>
        <label for="text"><?php echo JText::_('Note');?></label>
        <textarea rows="5" cols="50" name="text" id="text"></textarea>
        <input style="float:right; background-color: lightsteelblue; clear:both" type="submit"
               value="<?php echo JText::_('submit');?>">
    </form>
	<?php } ?>
    <br/>
	<?php $params = &JComponentHelper::getParams('com_j2mantis'); ?>
	<?php if ((string)$params->get('overview') != '0'): ?>
    <a href="?option=com_j2mantis&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"><?php echo JText::_('Return to Report Overview');?></a>
	<?php endif; ?>
</div>
