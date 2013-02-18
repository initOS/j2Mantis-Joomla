<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>


<?php JHTML::_('behavior.formvalidation') ?>
<?php JHTML::stylesheet('j2mantis.css', 'components/com_j2mantis/assets/'); ?>
<script type="text/javascript">
//<![CDATA[
function myValidate(f) {
        if (document.formvalidator.isValid(f)) {
                //f.check.value='<?php echo JUtility::getToken(); ?>';//send token
                return true; 
        }
        else {
                alert('<?php echo JText::_('please check the form');?>');
        }
        return false;
}
//]]>
</script>

<div id="j2Mantis" class="item-page">
<h2><?php echo $this->caption; ?></h2>

<?php if(!empty($_POST['errors'])): ?>
<div class="error"><h3><?php echo JText::_('Error');?></h3><ul>
<?php foreach($_POST['errors'] as $error){
	echo "<li>" . $error . "</li>";
}?>
</ul></div>
<?php endif; ?>



<form method="post" action="?option=com_j2mantis&amp;task=addBug&amp;Itemid=<?php echo JRequest::getInt('Itemid',0);?>"  onsubmit="return myValidate(this);">
<input type="hidden" name="view" value="addbug" />
<input type="hidden" name="check" value="post" />
<?php if( sizeof($this->project) > 1 ):?>

	<label for="project"><?php echo JText::_('Project');?></label>
	<select name="project" id="project" onchange="changeProject(this)">
		<?php foreach($this->project as $pid => $name){ ?>
					
			<option <?php if(!empty($_POST['project']) && $_POST['project'] ==$pid ) echo 'selected="selected"'; ?> value="<?php echo $pid; ?>"><?php echo $name ?></option>
					
		<?php } ?>
	</select >
	<br />
<?php else: ?>
	<?php foreach($this->project as $pid => $name){ ?>
		<input type="hidden" name="project" value="<?php echo $pid; ?>" />
	<?php } ?>
<?php endif; ?>
<label for="category"><?php echo JText::_('Category');?></label>
<select name="category" id="category">
	<?php foreach($this->cat as $id => $cArray){ ?>
		<?php foreach($cArray as $c){ ?>
			<option <?php if(!empty($_POST['category']) && $_POST['category'] ==$c ) echo 'selected="selected"'; ?> value="<?php echo $c; ?>" class="project-<?php echo $id; ?>"><?php echo  $c ?></option>
		<?php } ?>
	<?php } ?>
</select >
<br />
<label for="summary"><?php echo JText::_('Short Description');?>*</label>
<input type="text" name="summary" id="summary" class="required" <?php if(!empty($_POST['summary']))echo 'value="'.$_POST['summary'].'"' ?> />
<br />
<label for="name"><?php echo JText::_('Name');?>*</label>
<input type="text" name="name" id="name" class="required"  <?php if(!empty($_POST['name']))echo 'value="'.$_POST['name'].'"' ?>  />
<br />
<label for="email"><?php echo JText::_('E-Mail');?>*</label>
<input type="text" name="email" id="email" class="required validate-email"  <?php if(!empty($_POST['email']))echo 'value="'.$_POST['email'].'"' ?> />
<br />
<label for="priority"><?php echo JText::_('Priority');?></label>
<select name="priority" id="priority">
<option value="20" <?php if(!empty($_POST['priority']) && $_POST['priority'] ==20 ) echo 'selected="selected"'; ?>><?php echo JText::_('low');?></option>
<option value="30" <?php if(empty($_POST['priority']) or (!empty($_POST['priority']) && $_POST['priority'] ==30) ) echo 'selected="selected"'; ?>><?php echo JText::_('normal');?></option>
<option value="40" <?php if(!empty($_POST['priority']) && $_POST['priority'] ==40 ) echo 'selected="selected"'; ?>><?php echo JText::_('high');?></option>
</select>
<br />
<label for="description"><?php echo JText::_('Description');?></label>
<textarea name="description" id="description"  cols="50" rows="8"><?php if(!empty($_POST['description']))echo $_POST['description'] ?></textarea>
<br />
<?php $params = &JComponentHelper::getParams( 'com_j2mantis' ); ?>
<?php if($params->get('captcha')){ plgSystemJCCReCaptcha::display(); } ?>
<input type="submit" value="<?php echo JText::_('Submit');?>"/>
</form>
<?php if((boolean)$params->get('overview')): ?>
	<br />
	<a href="?option=com_j2mantis&amp;Itemid=<?php echo JRequest::getInt('Itemid',0);?>"><?php echo JText::_('Return to Report Overview');?></a>
<?php endif; ?>
<br style="clear: both;" />
</div>

<script type="text/javascript">
//<![CDATA[
var catogoryLists = new Array(<?php echo sizeof($this->project) ?>);
<?php foreach($this->project as $id => $name ): ?>
catogoryLists[<?php echo $id ?>] = ["<?php echo implode('","',$this->cat[$id]) ?>"];
<?php endforeach; ?>

function addNewOptions(id,projectId){
    var cList = catogoryLists[projectId];
    for( var i = 0; i < cList.length; i++ ){
        newOption = document.createElement("option");
         newOption.value = cList[i]; // assumes option string and value are the same
         newOption.text=cList[i];
        try{
            newOption.inject($('category'));
        }
        catch(ex){
            id.add(newOption);
        }
    }
}

function changeProject(el){
    var projectId = el.value;
    $('category').options.length = 0;
    addNewOptions($('category'),projectId);
}
changeProject( $('project') );
//]]>
</script>
