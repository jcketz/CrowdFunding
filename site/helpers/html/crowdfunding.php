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

/**
 * CrowdFunding Html Helper
 *
 * @package		ITPrism Components
 * @subpackage	CrowdFunding
 * @since		1.6
 */
abstract class JHtmlCrowdFunding {
    
    /**
     * 
     * Display an icon for approved or not approved project
     * @param integer $value
     */
    public static function approved($value) {
        
        $html = '<i class="{ICON}"></i>';
        
        switch($value) {
            
            case 1: // Published
                $html = str_replace("{ICON}", "icon-ok-sign", $html);
                break;
                
            default: // Unpublished
                $html = str_replace("{ICON}", "icon-remove-sign", $html);
                break;
        }
        
        return $html;
        
    }
    
    /**
     * 
     * Display an input field for amount
     * @param float $value
     * @param array $currency
     * @param array $options
     */
    public static function inputAmount($value, $currency, $options) {
        
        $class = "";
        if(!empty($currency["symbol"])){
            $class = "input-prepend ";
        }
        
        $class .= "input-append";
        
        $html = '<div class="'.$class.'">';
        
        if(!empty($currency["symbol"])){
            $html .= '<span class="add-on">'. $currency["symbol"] .'</span>';
        }
            
        $name = JArrayHelper::getValue($options, "name");
        
        $id   = "";
        if(JArrayHelper::getValue($options, "id")) {
            $id = 'id="'.JArrayHelper::getValue($options, "id").'"';
        }
        
        if(!$value OR !is_numeric($value)) {
            $value = 0;
        }
        
        if(JArrayHelper::getValue($options, "class")) {
            $class = 'class="'.JArrayHelper::getValue($options, "class").'"';
        }
        
        $html .= '<input type="text" name="'.$name.'" value="'.$value.'" '.$id.' '.$class.' />';
        
        if(!empty($currency["abbr"])) {
            $html .= '<span class="add-on">'.$currency["abbr"].'</span>';
        }
            
        $html .= '</div>';
        
        return $html;
        
    }
    
    /**
     * 
     * Add symbol or abbreviation to a currency
     * 
     * @param float $value
     * @param array $currency
     */
    public static function amount($value, $currency) {
        
        if(!empty($currency["symbol"])) { // Prepended
		    $amount = $currency["symbol"].$value;
		} else { // Append
		    $amount = $value.$currency["abbr"];
		}
		
		return $amount;
    } 
    
    /**
     * Display a progress bar
     * 
     * @param int 	  $percent	 	A percent of fund raising
     * @param int     $daysLeft
     * @param string  $fundingType
     */
    public static function progressBar($percent, $daysLeft, $fundingType) {
        
        $html   = array();
        $class  = 'progress-success';
        
        if($daysLeft > 0 ) {
            $html[1] = '<div class="bar" style="width: '.$percent.'%"></div>';
        } else {
            
            // Check for the type of funding
            if($fundingType == "FLEXIBLE") { 
            
                if($percent > 0 ) {
                    $html[1] = '<div class="bar" style="width: 100%">'.JString::strtoupper( JText::_("COM_CROWDFUNDING_SUCCESSFUL") ).'</div>';                
                } else {
                    $class   = 'progress-danger';
                    $html[1] = '<div class="bar" style="width: 100%">'.JString::strtoupper( JText::_("COM_CROWDFUNDING_COMPLETED") ).'</div>';
                }
                
            } else { // Fixed
                
                if($percent >= 100 ) {
                    $html[1] = '<div class="bar" style="width: 100%">'.JString::strtoupper( JText::_("COM_CROWDFUNDING_SUCCESSFUL") ).'</div>';                
                } else {
                    $class   = 'progress-danger';
                    $html[1] = '<div class="bar" style="width: 100%">'.JString::strtoupper( JText::_("COM_CROWDFUNDING_COMPLETED") ).'</div>';
                }
                
            }
            
        }
        
        $html[0] = '<div class="progress '.$class.'">';
        $html[2] = '</div>';
        
        ksort($html);
        
        return implode("\n", $html);
    } 
    
    /**
     * Display a state of result
     * 
     * @param int 	$percent	 	A percent of fund raising
     * @param string  $fundingType
     */
    public static function resultState($percent, $fundingType) {
        
        $html   = array();
        
        // Check for the type of funding
        if($fundingType == "FLEXIBLE") { 
            
            if($percent > 0 ) {
                $otuput = JText::_("COM_CROWDFUNDING_SUCCESSFUL");                
            } else {
                $otuput = JText::_("COM_CROWDFUNDING_COMPLETED");
            }
            
        } else { // Fixed
            
            if($percent >= 100 ) {
                $otuput = JText::_("COM_CROWDFUNDING_SUCCESSFUL");                
            } else {
                $otuput = JText::_("COM_CROWDFUNDING_COMPLETED");
            }
            
        }
        
        return $otuput;
    } 
    
    /**
     * 
     * Display a text that describes the state of result
     * 
     * @param int 	  $percent	 A percent of fund raising
     * @param string  $fundingType
     */
    public static function resultStateText($percent, $fundingType) {
        
        $html   = array();
        
        // Check for the type of funding
        if($fundingType == "FLEXIBLE") { 
            
            if($percent > 0 ) {
                $otuput = JText::_("COM_CROWDFUNDING_FUNDRAISE_FINISHED_SUCCESSFULLY");                
            } else {
                $otuput = JText::_("COM_CROWDFUNDING_FUNDRAISE_HAS_EXPIRED");
            }
            
        } else { // Fixed
            
            if($percent >= 100 ) {
                $otuput = JText::_("COM_CROWDFUNDING_FUNDRAISE_FINISHED_SUCCESSFULLY");                
            } else {
                $otuput = JText::_("COM_CROWDFUNDING_FUNDRAISE_HAS_EXPIRED");
            }
            
        }
        
        return $otuput;
    } 
    
    /**
     * 
     * Display an icon for state of project
     * @param integer $value
     * @param string  $url		An url to the task
     */
    public static function state($value, $url) {
        
        $html = '<a href="'.$url.'"><i class="{ICON}"></i></a>';
        
        switch($value) {
            
            case 1: // Published
                $html = str_replace("{ICON}", "icon-ok-circle", $html);
                break;
                
            default: // Unpublished
                $html = str_replace("{ICON}", "icon-remove-circle", $html);
                break;
        }
        
        return $html;
    }
    

	public static function approvedBackend($i, $value, $prefix, $checkbox = 'cb') {
	    
	    if(!$value) { // Disapproved
		    $task   = $prefix."approve";
		    $title  = "COM_CROWDFUNDING_APPROVE_ITEM";
		    $text   = "COM_CROWDFUNDING_DISAPPROVED";
		    $class  = "disapprove";
	    } else {
	        $task   = $prefix."disapprove";
	        $title  = "COM_CROWDFUNDING_DISAPPROVE_ITEM";
	        $text   = "COM_CROWDFUNDING_APPROVED";
	        $class  = "approve";
	    }
		
		$html[] = '<a class="jgrid hasTip"';
		$html[] = ' href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $task . '\')"';
		$html[] = ' title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
		$html[] = '<span class="state ' . $class . '">';
		$html[] = '<span class="text">' . JText::_($text) . '</span>';
		$html[] = '</span>';
		$html[] = '</a>';
		
		return implode($html);
	}
	
    
    /**
     * 
     * If value is higher than 100, sets it to 100. 
     * This method validates percent of funding.
     * @param integer $value
     */
    public static function funded($value) {
		if($value > 100) {
		    $value = 100;
		};
		return $value;
    }
    
    /**
     * Calculate funded percents
     * @param float $goal
     * @param float $funded
     */
    public static function percents($goal, $funded) {
		
        $percents = 0;
        if($goal > 0) {
            $percents = round( ($funded/$goal) * 100, 2 );
        }
        
		return $percents;
    }
    
    /**
     * 
     * This method generates a code that display a video
     * @param string $value
     */
    public static function video($value) {
        
        $uri  = new JURI($value);
        $host = $uri->getHost();
        
        $html = "";
        
        // Youtube
        if(false !== strpos($host, "youtu")) {
            
            $id = "";
            if( preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#', $value, $matches) ) {
                $id = $matches[0];
            }
                
            $html = '<iframe width="560" height="315" src="http://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
            
        }
        
        return $html;
    }
    
	/**
     * Provide a link to social profile. 
     * This method integreate users with profiles 
     * of some Joomla! social extensions.
     * 
     * @param int 	$value
     * @param string $extension
     */
    public static function socialProfile($value, $extension) {
        
        $link = "";
        
        switch($extension) {
            
            case "com_socialcommunity": // Social Community
                
                if(!defined("SOCIALCOMMUNITY_COMPONENT_SITE")) {
                    define("SOCIALCOMMUNITY_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_socialcommunity");
                }

                JLoader::register("SocialCommunityHelperRoute", SOCIALCOMMUNITY_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");
                $link = JRoute::_(SocialCommunityHelperRoute::getProfileRoute($value));
                
                break;
                
            default: 
                break;
        }
        
        return $link;
    }
}
