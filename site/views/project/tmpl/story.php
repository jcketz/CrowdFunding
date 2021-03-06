<?php
/**
 * @package      ITPrism Components
 * @subpackage   CrowdFunding
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * CrowdFunding is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php echo $this->loadTemplate("nav");?>
<div class="row-fluid">
    <form action="<?php echo JRoute::_('index.php?option=com_crowdfunding'); ?>" method="post" name="projectForm" id="crowdf-story-form" class="form-validate" enctype="multipart/form-data">
        
        <div class="span6">
            <?php echo $this->form->getLabel('pitch_video'); ?>
            <?php echo $this->form->getInput('pitch_video'); ?>
            <span class="help-block"><?php echo JText::_("COM_CROWDFUNDING_FIELD_FUNDING_VIDEO_HELP_BLOCK");?></span>
            
            <?php echo $this->form->getLabel('pitch_image'); ?>
            <?php echo $this->form->getInput('pitch_image'); ?>
            <span class="help-block">(PNG, JPG, or GIF - <?php echo $this->pWidth; ?> x <?php echo $this->pHeight; ?> pixels) </span>
            
            <?php if(!empty($this->pitchImage)) {?>
            <img src="<?php echo $this->imageFolder."/".$this->pitchImage;?>" class="img-polaroid" />
            <?php if(!$this->debugMode) {?>
            <div class="clearfix">&nbsp;</div>
        	<a href="<?php echo JRoute::_("index.php?option=com_crowdfunding&task=story.removeImage&id=".$this->item->id);?>" class="btn btn-mini"><i class="icon-trash"></i> <?php echo JText::_("COM_CROWDFUNDING_REMOVE_IMAGE");?></a>
        	<?php }?>
            <?php }?>
            
            <div class="width_600px">
                <?php echo $this->form->getLabel('description'); ?>
                <?php echo $this->form->getInput('description'); ?>
            </div>
        	<div class="clearfix"></div>
            
            <?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="task" value="story.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <button type="submit" class="button button-large margin-tb-15px" <?php echo $this->disabledButton;?>>
            	<i class="icon-ok icon-white"></i>
                <?php echo JText::_("JSAVE")?>
            </button>
        </div>
        
        <div class="span6">
        </div>
        
    </form>
</div>
<?php echo $this->version->backlink;?>