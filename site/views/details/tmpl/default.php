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
defined('_JEXEC') or die;?>

<div class="cfdetails<?php echo $this->params->get("pageclass_sfx"); ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>
	
	
	<?php if($this->item->event->beforeDisplayContent) {?>
	<div class="row-fluid">
		<div class="span12">
    		<div class="cf-details-block cf-border-bottom"><?php echo $this->item->event->beforeDisplayContent;?></div>
    	</div>
	</div>
	<?php }?>
	
	<div class="row-fluid">
		<div class="span8">
        	<div class="cf-details-block">
        	<?php if(!$this->item->pitch_video) {
                echo JHtml::_("image", $this->imageFolder."/".$this->item->pitch_image, $this->escape($this->item->title), array("class"=>"img-polaroid"));   
        	} else {
        	    echo JHtml::_("crowdfunding.video", $this->item->pitch_video);
        	}?>
        	</div>
        	
        	<?php if($this->item->event->onContentAfterDisplayMedia) {?>
        	<?php echo $this->item->event->onContentAfterDisplayMedia;?>
        	<?php }?>
        	
        	<div class="cf-details-block cf-border-top">
        	<?php switch($this->screen) {
        	    
        	    case "updates":
        	        echo $this->loadTemplate("updates");
        	        break;
        	        
        	    case "comments":
        	        echo $this->loadTemplate("comments");
        	        break;
        	        
    	        case "funders":
        	        echo $this->loadTemplate("funders");
        	        break;
        	        
        	    default:
        	        echo $this->loadTemplate("home");
        	        break;
        	}?>
        	</div>
    	</div>
    	
    	<div class="span4">
    		<?php echo $this->loadTemplate("info");?>
    		<div class="clearfix">&nbsp;</div>
    		<?php echo $this->loadTemplate("rewards");?>
    	</div>
	</div>
</div>
<div class="clearfix">&nbsp;</div>