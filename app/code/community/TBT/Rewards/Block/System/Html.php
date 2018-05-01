<?php

/**
 * WDCA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   WDCA
 * @package    TBT_Enhancedgrid
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TBT_Rewards_Block_System_Html extends Mage_Adminhtml_Block_System_Config_Form_Field {

	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $version = Mage::getConfig ()->getNode ( 'modules/TBT_Rewards/version' );
        $widget = Mage::getBlockSingleton('rewards/manage_widget_loyalty')->toHtml();
        $switchMessage = '';

        $defaultFormJs = '';

        $html = <<<FEED
            </tbody></table>

            <div style='margin-top:30px; width:430px;'>
                <i>Sweet Tooth v{$version}. <a href='https://support.sweettoothrewards.com/forums/20680573-core-releases' target='_blank'>Click here for updates</a></i>
                <br/>
                {$widget}
            </div>

            <table><tbody>

<script type="text/javascript">//<![CDATA[

Element.prototype.triggerEvent = function(eventName)
{
    if (document.createEvent)
    {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent(eventName, true, true);

        return this.dispatchEvent(evt);
    }

    if (this.fireEvent){
        return this.fireEvent('on' + eventName);
    }
}

function respondToClick(event) {
    var key = event.which || event.keyCode;
    if(key === Event.KEY_RETURN){
          //triger the "Connect to Sweet Tooth" button
          $('btn_connect').triggerEvent('click');
    }

}

//Observer the enter keypress for the username and password field

if($('rewards_platform_username') !== undefined && $('rewards_platform_username') !== null){
        $('rewards_platform_username').observe('keypress', respondToClick);
 }

if($('rewards_platform_password') !== undefined && $('rewards_platform_password') !== null){
        $('rewards_platform_password').observe('keypress', respondToClick);
 }
        {$defaultFormJs}
        //]]></script>
FEED;

        return $html;
    }

	protected function _getDummyElement() {
		if (empty ( $this->_dummyElement )) {
			$this->_dummyElement = new Varien_Object ( array ('show_in_default' => 1, 'show_in_website' => 1 ) );
		}
		return $this->_dummyElement;
	}

	protected function _getFieldRenderer() {
		if (empty ( $this->_fieldRenderer )) {
			$this->_fieldRenderer = Mage::getBlockSingleton ( 'adminhtml/system_config_form_field' );
		}
		return $this->_fieldRenderer;
	}

	protected function _getFieldHtml($fieldset, $moduleName) {
		$configData = $this->getConfigData ();
		$path = 'advanced/modules_disable_output/' . $moduleName; //TODO: move as property of form
		$data = isset ( $configData [$path] ) ? $configData [$path] : array ();

		$e = $this->_getDummyElement ();

		$moduleKey = substr ( $moduleName, strpos ( $moduleName, '_' ) + 1 );
		$ver = (Mage::getConfig ()->getModuleConfig ( $moduleName )->version);

		if ($ver) {
			$field = $fieldset->addField ( $moduleName, 'label', array ('name' => 'ssssss', 'label' => $moduleName, 'value' => $ver ) )->setRenderer ( $this->_getFieldRenderer () );
			return $field->toHtml ();
		}
		return '';
	}

        protected function _getSwitchMessage()
        {
            $html = '';

            $separator = "?";
            $url = Mage::helper('adminhtml')->getUrl('rewardsadmin/manage_config_platform/switch');
            if (strpos($url, $separator) !== false) {
                $separator = "&";
            }
            $url .= $separator;

            if (!Mage::getStoreConfig('rewards/platform/is_connected')) {
                $onClickJs = <<<FEED
                    showConnect();
FEED;
                $html .= '<tr id="row_signup"><td>&nbsp;</td><td><div style="margin-top:5px;">
                Already have an account? <a url="#" onclick="'.$onClickJs.'" style="cursor: pointer;">Click here to Connect!</a>
                </div></td></tr>';

                $onClickJs = <<<FEED
                    showSignup();
FEED;
                $html .= '<tr id="row_connect"><td>&nbsp;</td><td><div style="margin-top:5px;">
                Don\'t have an account? <a url="#" onclick="'.$onClickJs.'" style="cursor: pointer;">Click here to signup!</a>
                    </div></td></tr>';

            }

            return $html;
        }

}
