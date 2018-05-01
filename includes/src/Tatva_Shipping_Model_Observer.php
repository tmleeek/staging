<?php

class Tatva_Shipping_Model_Observer
{
    private function getUrl($route = '', $params = array()) {
		return Mage::helper ( 'adminhtml' )->getUrl ( $route, $params );
	}
	
	public function test()
	{
		echo 'hey';exit;
	}

	public function checkConfig($observer)
    {
        if (Mage::getSingleton('admin/session')->getUser() || Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $this->getWeeklyUpdate();

			$_license_key = Mage::getStoreConfig( 'tatvasettings/license/key' );
            
            if($_license_key)
            {
                $valid = Mage::getStoreConfig( 'tatvasettings/license/valid' );
                $_license_key_back = Mage::getStoreConfig( 'tatvasettings/license/keyback' );

                /*comment by nisha */
				if($valid == md5(0) || empty($_license_key_back))
                {
                    $this->checkLicenseKey($_license_key);
                }
                else if($_license_key_back)
                {
                    if(md5($_license_key) != $_license_key_back)
                    {
                        $this->checkLicenseKey($_license_key);
                    }
                }
            }
            else
            {
                Mage::getSingleton ( 'adminhtml/session' )->addNotice (
						Mage::helper ( 'tatvashipping' )->__ ( 'Enter valid license key to enable the extension. : <a href="%s">configuration</a>', $this->getUrl ( 'adminhtml/system_config/edit', array (
								'section' => 'tatvasettings' ) ) ) );

                Mage::getModel('core/config')->saveConfig('tatvasettings/license/valid', md5(0) );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/enable', 0 );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/date', now() );
                Mage::getSingleton('core/session')->setData('license_key','');
                $this->getDisable();
                $this->refreshCache();
			}
		}
	}

    public function checkLicenseKey($_license_key)
    {
        $url = strtolower($_SERVER["HTTP_HOST"]);
        $url = ereg_replace('www\.' , '' , $url);
        $url = parse_url($url);

        if(!empty($url['host']))
        {
            $url = $url['host'];
        }
        else
        {
            $url = $url['path'];
        }

        $key = "key/".$_license_key."/";
        $domain = "domain/".$url;
        $url = "http://www.devagento.com/fr/license/license/index/".$key.$domain;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec ($ch);

        $contents = trim($contents);

        switch ($contents)
        {
            case 0:
                Mage::getSingleton ( 'adminhtml/session' )->addError (
                    Mage::helper ( 'tatvashipping' )->__ ( 'License key entered by you is not valid to enable the extension. : <a href="%s">configuration</a>', $this->getUrl ( 'adminhtml/system_config/edit', array (
                        'section' => 'tatvasettings' ) ) ) );

                Mage::getModel('core/config')->saveConfig('tatvasettings/license/valid', md5(0) );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/enable', 0 );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/key', '' );

                $this->getDisable();
                $this->refreshCache();
                break;

            case 1:
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess (
                Mage::helper ( 'tatvashipping' )->__ ( 'License key is successfully installed to enable the extension.' ) );

                Mage::getModel('core/config')->saveConfig('tatvasettings/license/valid', md5(1) );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/enable', 1 );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/date', now() );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/keyback', md5($_license_key) );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/keyback2', $_license_key );
                $this->getEnable();
                $this->refreshCache();
                break;

            case 2:
                Mage::getSingleton ( 'adminhtml/session' )->addError (
                    Mage::helper ( 'tatvashipping' )->__ ( 'License key is already utilised and exceed the limit. : <a href="%s">configuration</a>', $this->getUrl ( 'adminhtml/system_config/edit', array (
                        'section' => 'tatvasettings' ) ) ) );

                Mage::getModel('core/config')->saveConfig('tatvasettings/license/valid', md5(0) );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/enable', 0 );
                Mage::getModel('core/config')->saveConfig('tatvasettings/license/key', '' );

                $this->getDisable();
                $this->refreshCache();
                break;

            case 3:
                Mage::getSingleton ( 'adminhtml/session' )->addNotice (
                Mage::helper ( 'tatvashipping' )->__ ( 'Your domain is already registered with valid License key. No need to register it again.' ) );

                if(Mage::getStoreConfig( 'tatvasettings/license/keyback2' ))
                {
                    Mage::getModel('core/config')->saveConfig('tatvasettings/license/key', Mage::getStoreConfig( 'tatvasettings/license/keyback2' ) );
                }

                $this->refreshCache();
                break;
        }

        curl_close ($ch);
    }
    //comment by nisha public function getWeeklyUpdate($observer)
    public function getWeeklyUpdate()
    {
        $past_date = Mage::getStoreConfig( 'tatvasettings/license/date' );
        $date_diff = $this->dateDiff($past_date, now());
        if($date_diff == 7)
        {
            Mage::getModel('core/config')->saveConfig('tatvasettings/license/valid', md5(0) );
            Mage::getModel('core/config')->saveConfig('tatvasettings/license/enable', 0 );
        }
    }

    public function dateDiff($start, $end)
    {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    public function refreshCache()
    {
        $allTypes = Mage::app()->useCache();
        foreach($allTypes as $type => $blah)
        {
            Mage::app()->getCacheInstance()->cleanType($type);
        }
    }

    public function getEnable()
    {
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Adminhtml', 0 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Attachpdf', 0 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Checkout', 0 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Customer', 0 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Shipping', 0 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Tax', 0 );

        Mage::getModel('core/config')->saveConfig('carriers/colissimo/active', 1 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimopostoffice/active', 1 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimocityssimo/active', 1 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimoappointment/active', 1 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimolocalstore/active', 1 );
    }

    public function getDisable()
    {
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Adminhtml', 1 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Attachpdf', 1 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Checkout', 1 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Customer', 1 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Shipping', 1 );
        Mage::getModel('core/config')->saveConfig('advanced/modules_disable_output/Tatva_Tax', 1 );

        Mage::getModel('core/config')->saveConfig('carriers/colissimo/active', 0 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimopostoffice/active', 0 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimocityssimo/active', 0 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimoappointment/active', 0 );
        Mage::getModel('core/config')->saveConfig('carriers/colissimolocalstore/active', 0 );
    }
}