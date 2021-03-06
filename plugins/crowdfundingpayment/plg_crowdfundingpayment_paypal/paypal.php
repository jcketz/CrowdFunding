<?php
/**
 * @package		 ITPrism Plugins
 * @subpackage	 CrowdFunding Payment 
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

jimport('joomla.plugin.plugin');

/**
 * CrowdFunding Payment Plugin
 *
 * @package		ITPrism Plugins
 * @subpackage	CrowdFunding
 */
class plgCrowdFundingPaymentPayPal extends JPlugin {
    
    
    public function onProjectPayment($context, $item) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("html", $docType) != 0){
            return;
        }
       
        if(strcmp("com_crowdfunding.payment", $context) != 0){
            return;
        }
        
        // Load language
        $this->loadLanguage();
        
        $notifyUrl = $this->getNotifyUrl();
        $returnUrl = $this->getReturnUrl($item->slug, $item->catslug);
        $cancelUrl = $this->getCancelUrl($item->slug, $item->catslug);
        
        $html  =  "";
        $html .= '<h4>'.JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TITLE").'</h4>';
        $html .= '<p>'.JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_INFO").'</p>';
        
        if(!$this->params->get('paypal_sandbox', 1)) {
            $html .= '<form action="'.$this->params->get('paypal_url').'" method="post">';
            $html .= '<input type="hidden" name="business" value="'.$this->params->get('paypal_business_name').'" />';
        }  else {
            $html .= '<form action="'.$this->params->get('paypal_sandbox_url').'" method="post">';
            $html .= '<input type="hidden" name="business" value="'.$this->params->get('paypal_sandbox_business_name').'" />';
        }
        
        $html .= '<input type="hidden" name="cmd" value="_xclick" />';
        $html .= '<input type="hidden" name="charset" value="utf-8" />';
        $html .= '<input type="hidden" name="currency_code" value="'.$item->currencyCode.'" />';
        $html .= '<input type="hidden" name="amount" value="'.$item->amount.'" />';
        $html .= '<input type="hidden" name="quantity" value="1" />';
        $html .= '<input type="hidden" name="no_shipping" value="1" />';
        $html .= '<input type="hidden" name="no_note" value="1" />';
        $html .= '<input type="hidden" name="tax" value="0" />';
        
        // Title
        $title = JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_INVESTING_IN_S", htmlentities($item->title, ENT_QUOTES, "UTF-8"));
        $html .= '<input type="hidden" name="item_name" value="'.$title.'" />';
        
        $userId = JFactory::getUser()->id;
        
        // Custom data
        $custom = array(
            "project_id" =>  $item->id,
            "reward_id"  =>  $item->rewardId,
            "user_id"    =>  $userId,
            "gateway"	 =>  "PayPal"
        );
        
        $custom = base64_encode( json_encode($custom) );
        
        $html .= '<input type="hidden" name="custom" value="'.$custom.'" />';
        
        if($this->params->get('paypal_image_url')) {
            $html .= '<input type="hidden" name="image_url" value="'.$this->params->get('paypal_image_url').'" />';
        }
        
        if($this->params->get('paypal_cpp_headerback_color')) {
            $html .= '<input type="hidden" name="cpp_headerback_color" value='.$this->params->get('paypal_cpp_headerback_color').'" />';
        }
        
        $html .= '<input type="hidden" name="cancel_return" value="'.$cancelUrl.'" />';
        
        $html .= '<input type="hidden" name="return" value="'.$returnUrl.'" />';
        
        $html .= '<input type="hidden" name="notify_url" value="'.$notifyUrl.'" />';
        $html .= '<input type="image" name="submit" border="0" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="'.JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_BUTTON_ALT").'">
        <img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >  
    	</form>';
        
        if($this->params->get('paypal_sandbox', 1)) {
            $html .= '<p class="sticky">'.JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_WORKS_SANDBOX").'</p>';
        }
        
        return $html;
        
    }
    
    /**
     * 
     * Enter description here ...
     * @param array 	$post	This is _POST variable
     * @param JRegistry $params	The parameters of the component
     */
    public function onPaymenNotify($context, $post, $params) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("raw", $docType) != 0){
            return;
        }
       
        if(strcmp("com_crowdfunding.notify", $context) != 0){
            return;
        }
        
        // Verify gateway. Is it PayPal? 
        if(!$this->isPayPalGateway($post)) {
            return null;
        }
        
        // Load language
        $this->loadLanguage();
        
        // Get PayPal URL
        $sandbox      = $this->params->get('paypal_sandbox', 0); 
        if(!$sandbox) { 
            $url = $this->params->get('paypal_url', "https://www.paypal.com/cgi-bin/webscr"); 
        } else { 
            $url = $this->params->get('paypal_sandbox_url', "https://www.sandbox.paypal.com/cgi-bin/webscr");
        }
        
        jimport("itprism.paypal.verify");
        $paypalVerify = new ITPrismPayPalVerify($url, $post);
        $paypalVerify->verify();
        
        $result = array(
        	"project"     => null, 
        	"reward"      => null, 
        	"transaction" => null
        );
        
        if($paypalVerify->isVerified()) {
            
            // Get extension parameters
            $currencyId  = $params->get("project_currency");
            $currency    = CrowdFundingHelper::getCurrency($currencyId);

            // Validate transaction data
            $validData = $this->validateData($post, $currency["abbr"]);
            if(is_null($validData)) {
                return $result;
            }
            
            // Check for valid project
            jimport("crowdfunding.project");
            $projectId = JArrayHelper::getValue($validData, "project_id");
            
            $db        = JFactory::getDbo();
            $project   = new CrowdFundingProject($db);
            $project->load($projectId);
            
            if(!$project->id) {
                $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_PROJECT");
                $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($validData, true));
    			JLog::add($error);
    			return $result;
            }
            
            // Set the receiver of funds
            $validData["receiver_id"] = $project->user_id;
            
            // Validate and Update distributed value of the reward
            $rewardId  = JArrayHelper::getValue($validData, "reward_id");
            $reward    = null;
            if(!empty($rewardId)) {
                $reward = $this->updateReward($validData);
            }
        
            // Save transaction data
            $this->save($validData, $project);
            
            //  Prepare the data that will be returned
            
            $result["transaction"] = JArrayHelper::toObject($validData);
            
            // Generate object of data based on the project properties
            $properties            = $project->getProperties();
            $result["project"]     = JArrayHelper::toObject($properties);
            
            // Generate object of data based on the reward properties
            if(!empty($reward)) {
                $properties        = $reward->getProperties();
                $result["reward"]  = JArrayHelper::toObject($properties);
            }
        }
        
        return $result;
                
    }
    
	/**
     * Validate PayPal transaction
     * @param array $data
     */
    protected function validateData($data, $currency) {
        
        // Prepare transaction data
        $custom    = JArrayHelper::getValue($data, "custom");
        $custom    = json_decode( base64_decode($custom), true );
        
        // Prepare transaction data
        $transaction = array(
            "investor_id"		     => JArrayHelper::getValue($custom, "user_id", 0, "int"),
            "project_id"		     => JArrayHelper::getValue($custom, "project_id", 0, "int"),
            "reward_id"			     => JArrayHelper::getValue($custom, "reward_id", 0, "int"),
        	"service_provider"       => "PayPal",
        	"txn_id"                 => JArrayHelper::getValue($data, "txn_id"),
        	"txn_amount"		     => JArrayHelper::getValue($data, "mc_gross"),
            "txn_currency"           => JArrayHelper::getValue($data, "mc_currency"),
            "txn_status"             => strtolower( JArrayHelper::getValue($data, "payment_status") ),
        ); 
        
        // Check User Id, Project ID and Transaction ID
        if(!$transaction["investor_id"] OR !$transaction["project_id"] OR !$transaction["txn_id"]) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_TRANSACTION_DATA");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        // Check currency
        if(strcmp($transaction["txn_currency"], $currency) != 0) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_TRANSACTION_CURRENCY");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        // Check receiver
        $allowedReceivers = array(
            JArrayHelper::getValue($data, "business"),
            JArrayHelper::getValue($data, "receiver_email"),
            JArrayHelper::getValue($data, "receiver_id")
        );
        
        if($this->params->get("paypal_sandbox", 0)) {
            $receiver = $this->params->get("paypal_sandbox_business_name");
        } else {
            $receiver = $this->params->get("paypal_business_name");
        }
        if(!in_array($receiver, $allowedReceivers)) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_RECEIVER");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($transaction, true));
            JLog::add($error);
            return null;
        }
        
        return $transaction;
    }
    
    protected function updateReward(&$data) {
        
        $db     = JFactory::getDbo();
        
        jimport("crowdfunding.reward");
        $reward = new CrowdFundingReward($db);
        $keys   = array(
        	"id"         => $data["reward_id"], 
        	"project_id" => $data["project_id"]
        );
        $reward->load($keys);
        
        // Check for valid reward
        if(!$reward->id) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_REWARD");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($data, true));
			JLog::add($error);
			
			$data["reward_id"] = 0;
			return null;
        }
        
        // Check for valida amount between reward value and payed by user
        $txnAmount = JArrayHelper::getValue($data, "txn_amount");
        
        if($txnAmount < $reward->amount) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_INVALID_REWARD_AMOUNT");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($data, true));
			JLog::add($error);
			
			$data["reward_id"] = 0;
			return null;
        }
        
        if($reward->isLimited() AND !$reward->getAvailable()) {
            $error  = JText::_("PLG_CROWDFUNDINGPAYMENT_PAYPAL_ERROR_REWARD_NOT_AVAILABLE");
            $error .= "\n". JText::sprintf("PLG_CROWDFUNDINGPAYMENT_PAYPAL_TRANSACTION_DATA", var_export($data, true));
			JLog::add($error);
			
			$data["reward_id"] = 0;
			return null;
        }
        
        // Increase the number of distributed rewards 
        // if there is a limit.
        if($reward->isLimited()) {
            $reward->increaseDistributed();
            $reward->store();
        }
        
        return $reward;
    }
    
    /**
     * 
     * Save transaction
     * @param array $data
     */
    public function save($data, $project) {
        
        // Save data about donation
        $db     = JFactory::getDbo();
        
        jimport("crowdfunding.transaction");
        $transaction = new CrowdFundingTransaction($db);
        $transaction->bind($data);
        $transaction->store();
        
        // Update project funded amount
        $amount = JArrayHelper::getValue($data, "txn_amount");
        $project->addFunds($amount);
        $project->store();
    }
    
    private function getNotifyUrl() {
        
        $notifyPage = $this->params->get('paypal_notify_url');
        $uri        = JFactory::getURI();
        
        $domain     = $uri->toString(array("host"));
        
        if( false == strpos($notifyPage, $domain) ) {
            $notifyPage = $uri->toString(array("scheme", "host"))."/".str_replace("&", "&amp;", $notifyPage);
        }
        
        return $notifyPage;
        
    }
    
    private function getReturnUrl($slug, $catslug) {
        
        $returnPage = $this->params->get('paypal_return_url');
        if(!$returnPage) {
            $uri        = JFactory::getURI();
            $returnPage = $uri->toString(array("scheme", "host")).JRoute::_(CrowdFundingHelperRoute::getBackingRoute($slug, $catslug)."&layout=share", false);
        } 
        
        return $returnPage;
        
    }
    
    private function getCancelUrl($slug, $catslug) {
        
        $cancelPage = $this->params->get('paypal_cancel_url');
        if(!$cancelPage) {
            $uri        = JFactory::getURI();
            $cancelPage = $uri->toString(array("scheme", "host")).JRoute::_(CrowdFundingHelperRoute::getBackingRoute($slug, $catslug)."&layout=default", false);
        } 
        
        return $cancelPage;
    }
    
    private function isPayPalGateway($post) {
        
        $custom         = JArrayHelper::getValue($post, "custom");
        $custom         = json_decode( base64_decode($custom), true );
        $paymentGateway = JArrayHelper::getValue($custom, "gateway");

        if(strcmp("PayPal", $paymentGateway) != 0 ) {
            return false;
        }
        
        return true;
    }
    
}