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

jimport('joomla.application.component.modelform');

class CrowdFundingModelUpdate extends JModelForm {
    
    protected $item = null;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Update', $prefix = 'CrowdFundingTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
	/**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     * @since	1.6
     */
    protected function populateState() {
        
        parent::populateState();
        
        $app = JFactory::getApplication("Site");
        /** @var $app JSite **/
        
		// Get the pk of the record from the request.
		$value = $app->input->getInt("id");
		$this->setState($this->getName() . '.id', $value);
		
		$value = $app->input->getInt("project_id");
		$this->setState('project_id', $value);

		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
		
    }
    
    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm($this->option.'.update', 'update', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		$data	    = $app->getUserState($this->option.'.edit.update.data', array());
		if(!$data) {
		    
		    $itemId = $this->getState($this->getName().'.id');
		    $userId = JFactory::getUser()->id;
		    
		    $data   = $this->getItem($itemId, $userId);
		    
		}

		return $data;
    }
    
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  	  The id of the primary key.
	 * @param   integer  $userId  The user Id 
	 *
	 * @since   11.1
	 */
	public function getItem($pk, $userId) {
	    
	    if($this->item) {
	        return $this->item;
	    }
	    
		// Initialise variables.
		$table = $this->getTable();

		if ($pk > 0 AND $userId > 0) {
		    
		    $keys = array(
		    	"id"     => $pk, 
		    	"user_id"=> $userId
		    );
		    
			// Attempt to load the row.
			$return = $table->load($keys);

			// Check for a table object error.
			if ($return === false && $table->getError()) {
			    JLog::add($table->getError() . " [ CrowdFundingUpdate->getItem() ]");
				throw new Exception(JText::_("COM_CROWDFUNDING_ERROR_SYSTEM"), ITPrismErrors::CODE_ERROR);
			}
			
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties();
		$this->item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($this->item, 'params')) {
			$registry = new JRegistry;
			$registry->loadString($this->item->params);
			$this->item->params = $registry->toArray();
		}
		
		return $this->item;
	}
	
    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The record id on success, null on failure.
     * @since	1.6
     */
    public function save($data, $params = null) {
        
        $id          = JArrayHelper::getValue($data, "id");
        $title       = JArrayHelper::getValue($data, "title");
        $desc        = JArrayHelper::getValue($data, "description");
        $projectId   = JArrayHelper::getValue($data, "project_id");
        
        $userId      = JFactory::getUser()->id;
        
        $keys = array(
	    	"id"      => $id, 
	    	"user_id" => $userId
	    );
	    
        // Load a record from the database
        $row = $this->getTable();
        $row->load($keys);
        
        $row->set("title",             $title);
        $row->set("description",       $desc);
        
        if(!$row->user_id) {
            $row->set("record_date",   null);
            $row->set("project_id",    $projectId);
            $row->set("user_id",       $userId);
        }
        
        $row->store();
        
        return $row->id;
        
    }
    
    
}