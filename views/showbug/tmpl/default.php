<?php

// No direct access

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::stylesheet('j2mantis.css', 'components/com_j2mantis/assets/'); ?>
<div id="j2Mantis">
    <!-- <a href="?option=com_j2mantis" style="float: right">zurück zur Übersicht</a> -->
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
				<?php echo gmdate("d.m.Y H:i", strtotime($this->bug->last_updated));?>
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
		<?php if (!empty($this->bug->additional_information)) {
		echo '
<tr>
	<td colspan="3">' .
			nl2br($this->bug->additional_information) . '
	</td>
</tr>';
	} ?>
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
                    <span><?php echo gmdate("d.m.Y H:i", strtotime($note->date_submitted));?></span>
                </td>
                <td>
					<?php echo nl2br($note->text); //var_dump($note)?>
                </td>
            </tr>
			<?php } ?>
        </table>
		<?php } ?>

	<?php if ($this->bug->status->id < 80) { /* FIXME: check if closed */ ?>
    <br/>
    <h2><?php echo JText::_('Action holder & due date');?></h2>
    <form method="post"
          action="?option=com_j2mantis&amp;task=editBug&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"
          enctype="multipart/form-data">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="duedate"><?php echo JText::_('Due date');?></label>
		<?php echo JHTML::_('calendar', $this->due_date, 'due_date', 'dd_id', '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10')); ?>
        <br/>
        <input type="submit" value="<?php echo JText::_('submit');?>">
        <br/>
		<?php if ( isset($this->actionholders) ) { ?>
<!--		TODO: get default value -->
        <label>Actionholder:</label>
        <select name="actionholderId" STYLE="width: 200px">
            <option value="-1">- none -</option>
			<?php foreach($this->actionholders as $actionholder){ ?>
            <option value="<?php echo $actionholder->id; ?>"><?php echo $actionholder->name; ?></option>
			<?php } ?>
        </select>
        <br/>
		<?php } ?>
    </form>
    <h2><?php echo JText::_('attach File');?></h2>
    <form method="post"
          action="?option=com_j2mantis&amp;task=addFile&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"
          enctype="multipart/form-data">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="name">File:</label>
        <input type="file" name="name" size="30" id="name" maxlength="100000"/><br/>
        <input type="submit" value="<?php echo JText::_('submit');?>">
    </form>

    <br/>
    <h2><?php echo JText::_('add Note');?></h2>
    <form method="post"
          action="?option=com_j2mantis&amp;task=addNote&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>">
        <input type="hidden" name="bugid" value="<?php echo $this->bug->id ?>"/>
        <label for="name"><?php echo JText::_('Name'); if ( $this->fo_name == 1 ) echo '*';?></label>
        <input type="text" name="name" id="name" <?php if ( $this->fo_name == 1 ) echo 'class="required"'; if ( ! $this->fo_nameedit ) echo ' readonly'; ?>
			<?php if (!empty($this->user->name)) {
			echo 'value="' . $this->user->name . ' [' . $this->user->username . ']"';
		} ?>  />
        <br/>
        <label for="text"><?php echo JText::_('Note');?></label>
        <textarea rows="5" cols="50" name="text" id="text"></textarea>
        <input type="submit" value="<?php echo JText::_('submit');?>">
    </form>
	<?php } ?>
    <br/>
	<?php $params = &JComponentHelper::getParams('com_j2mantis'); ?>
	<?php if ((string)$params->get('overview') != '0'): ?>
    <a href="?option=com_j2mantis&amp;Itemid=<?php echo JRequest::getInt('Itemid', 0);?>"><?php echo JText::_('Return to Report Overview');?></a>
	<?php endif; ?>
</div>
