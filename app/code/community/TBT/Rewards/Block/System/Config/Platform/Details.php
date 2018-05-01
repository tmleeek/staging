<?php

class TBT_Rewards_Block_System_Config_Platform_Details extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const ATTRIBUTE_FIRSTNAME = 'firstname';
    const ATTRIBUTE_LASTNAME = 'lastname';
    const ATTRIBUTE_EMAIL = 'email';
    const ATTRIBUTE_USERNAME = 'username';
    const ATTRIBUTE_URL = 'login_url';
    const ATTRIBUTE_SERVER_STATUS = 'server_status';

    protected $_account = null;

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';

        if ($this->_checkElement($element, self::ATTRIBUTE_FIRSTNAME)) {
            $html = $this->_getAccountAttribute(self::ATTRIBUTE_FIRSTNAME);
        } else if ($this->_checkElement($element, self::ATTRIBUTE_LASTNAME)) {
            $html = $this->_getAccountAttribute(self::ATTRIBUTE_LASTNAME);
        } else if ($this->_checkElement($element, self::ATTRIBUTE_EMAIL)) {
            $html = $this->_getAccountAttribute(self::ATTRIBUTE_EMAIL);
        } else if ($this->_checkElement($element, self::ATTRIBUTE_USERNAME)) {
            $html = $this->_getAccountAttribute(self::ATTRIBUTE_USERNAME);
        } else if ($this->_checkElement($element, self::ATTRIBUTE_URL)) {
            $html = $this->_htmlAnchor($this->_getBaseURL($this->_getAccountAttribute(self::ATTRIBUTE_URL)));
        } else if ($this->_checkElement($element, self::ATTRIBUTE_SERVER_STATUS)) {
            $html = $this->_getServerStatusHtml();
        }

        return $html;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getId();
        $html = '';

        if (Mage::getStoreConfig('rewards/platform/is_connected')) {
            $element->setScopeLabel('');

            $platform = Mage::getSingleton('rewards/platform_instance');
            try {
                $this->_account = $platform->account()->get();
            } catch (Exception $ex) {
                // we don't add a warning to the session because it's too late for that
                $element->setId($id . '_' . self::ATTRIBUTE_SERVER_STATUS);
                $element->setLabel($this->__("Server Status"));
                $html .= parent::render($element);
            }

            $element->setId($id . '_' . self::ATTRIBUTE_FIRSTNAME);
            $element->setLabel($this->__("First Name"));
            $html .= ($this->_hasAccountAttribute(self::ATTRIBUTE_FIRSTNAME) ? parent::render($element) : "");

            $element->setId($id . '_' . self::ATTRIBUTE_LASTNAME);
            $element->setLabel($this->__("Last Name"));
            $html .= ($this->_hasAccountAttribute(self::ATTRIBUTE_LASTNAME) ? parent::render($element) : "");

            $element->setId($id . '_' . self::ATTRIBUTE_EMAIL);
            $element->setLabel($this->__("E-mail"));
            $html .= ($this->_hasAccountAttribute(self::ATTRIBUTE_EMAIL) ? parent::render($element) : "");

            $element->setId($id . '_' . self::ATTRIBUTE_USERNAME);
            $element->setLabel($this->__("Username"));
            $html .= ($this->_hasAccountAttribute(self::ATTRIBUTE_USERNAME) ? parent::render($element) : "");

            $element->setId($id . '_' . self::ATTRIBUTE_URL);
            $element->setLabel($this->__("Account URL"));
            $html .= ($this->_hasAccountAttribute(self::ATTRIBUTE_URL) ? parent::render($element) : "");
        }

        return $html;
    }

    protected function _checkElement(Varien_Data_Form_Element_Abstract $element, $htmlIdSuffix)
    {
        return substr_compare($element->getId(), $htmlIdSuffix, -strlen($htmlIdSuffix), strlen($htmlIdSuffix)) === 0;
    }

    protected function _hasAccountAttribute($attribute)
    {
        if ($this->_account == null || !array_key_exists($attribute, $this->_account) || empty($this->_account[$attribute])) {
            return false;
        }

        return true;
    }

    protected function _getAccountAttribute($attribute)
    {
        if (!$this->_hasAccountAttribute($attribute)) {
            return '<i>' . $this->__("Not Set") . '</i>';
        }

        return $this->_account[$attribute];
    }

    protected function _getBaseURL($fullURL, $includeScheme = true){
        $url = parse_url($fullURL);
        return ($url === false) ? "" : (($includeScheme ? ($url['scheme'] . "://") : "") . $url['host']);
    }

    protected function _htmlAnchor($link, $showScheme = false){
        return $link ? '<a href="'.$link.'" target="_blank">'.$this->_getBaseURL($link, $showScheme).'</a>' : '';
    }

    protected function _getServerStatusHtml()
    {
        if ($this->_account == null) {
            $status = "<span style='color:#FF0000; font-weight:bold;'>" . $this->__("Unavailable") . "</span>";
            $status .= '<p class="note"><span>'
                .'We\'re having trouble reaching Sweet Tooth servers at the moment.<br />'
                .' Sweet Tooth functionality should not be affected by this, but if you continue to see this message for longer than a few hours, contact Sweet Tooth support.'
                .'</span></p>';


            return $status;
        }

        return "<span style='color:#008800; font-weight:bold;'>" . $this->__("Good") . "</span>";
    }
}
