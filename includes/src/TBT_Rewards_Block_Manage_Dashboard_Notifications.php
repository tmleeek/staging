<?php

class TBT_Rewards_Block_Manage_Dashboard_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Notifications array
     * @var array
     */
    protected $_notifications = array();
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/dashboard/notifications.phtml');
    }
    
    /**
     * Retrieve all active notifications
     * 
     * @return array Contains all notifications
     */
    public function getNotifications()
    {
        return $this->_notifications;
    }
    
    /**
     * Add a new notification
     * @param text $notifText       The notification text displayed to the user
     * @param text $notifLink       The notification link
     * @param text $notifLinkText   The text to be displayed for the link
     */
    public function setNotification ($notifText, $notifLink, $notifLinkText)
    {
        $notification = array(
            'text'      => $notifText,
            'link'      => $notifLink,
            'linkText'  => $notifLinkText
        );
        
        $this->_notifications[] = $notification;
        
        return $this;
    }
}
