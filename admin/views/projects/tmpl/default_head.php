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
<tr>
    <th width="1%">
        <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
    </th>
	<th class="title" >
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="20%">
	     <?php echo JText::_('JCATEGORY'); ?>
	</th>
	<th width="5%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_CREATED', 'a.created', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_GOAL', 'a.goal', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_FUNDED', 'a.funded', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_FUNDED_PERCENTS', 'funded_percents', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_START_DATE', 'a.funding_start', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%">
	     <?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_END_DATE', 'a.funding_end', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%">
	     <?php echo JText::_("COM_CROWDFUNDING_FUNDING_DAYS"); ?>
	</th>
	<th width="10%">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $this->listDirn, $this->listOrder); ?>
        <?php if ($this->saveOrder) {?>
        <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'campaigns.saveorder'); ?>
        <?php }?>
    </th>
    <th width="5%"><?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?></th>
    <th width="5%"><?php echo JHtml::_('grid.sort',  'COM_CROWDFUNDING_APPROVED', 'a.approved', $this->listDirn, $this->listOrder); ?></th>
    <th width="3%" class="nowrap"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?></th>
</tr>
	  