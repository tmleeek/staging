<?php

/**
 * Class MDN_Mpm_Block_Dashboard_Tabs
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Dashboard_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Construct
     */
    public function __construct()
    {

        parent::__construct();
        $this->setId('mpm_dashboard_tab');
        $this->setDestElementId('smartprice_dashboard_index_tab_content');
        $this->setTitle($this->__('Channel Information'));

    }

    /**
     * Before HTML
     *
     * @return type
     */
    protected function _beforeToHtml()
    {

        foreach(Mage::Helper('Mpm/Carl')->getChannelsSubscribed() as $channel){
            $channelCode = $channel->organization.'_'.$channel->locale.'_'.$channel->subset;
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/Mpm_Dashboard/channelBLock', array('channel' => $channelCode));
            $this->addTab(
                $channelCode,
                array(
                    'label'   => Mage::helper('Mpm/Channel')->getChannelNameFromChannelCode($channelCode),
                    'class' => 'ajax',
                    'url' => $url,
                )
            );

        }

        return parent::_beforeToHtml();
    }
}
