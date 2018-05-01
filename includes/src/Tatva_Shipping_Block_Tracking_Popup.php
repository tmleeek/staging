<?php

class Tatva_Shipping_Block_Tracking_Popup extends Mage_Shipping_Block_Tracking_Popup
{
    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        $helper = Mage::helper('shipping');
        $data = $helper->decodeTrackingHash($this->getRequest()->getParam('hash'));
        if (!empty($data))
        {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getOrderId()>0)
            {
                return $this->getTrackingInfoByOrder();
            }
            elseif($this->getShipId()>0)
            {
                return $this->getTrackingInfoByShip();
            }
            else
            {
                return $this->getTrackingInfoByTrackId();
            }
        }
        else
        {
            $this->setOrderId($this->getRequest()->getParam('order_id'));
            $this->setTrackId($this->getRequest()->getParam('track_id'));
            $this->setShipId($this->getRequest()->getParam('ship_id'));

            if ($this->getOrderId()>0)
            {
                return $this->getTrackingInfoByOrder();
            }
            elseif($this->getShipId()>0)
            {
                return $this->getTrackingInfoByShip();
            }
            else
            {
                return $this->getTrackingInfoByTrackId();
            }
        }
    }

    /*
    * retrieve all tracking by orders id
    */
    public function getTrackingInfoByOrder()
    {
    	$shipTrack = array();
        $order = $this->_initOrder();
    	$shipments = $order->getShipmentsCollection();
    	foreach ($shipments as $shipment)
        {
			$increment_id = $shipment->getIncrementId();
			$tracks = $shipment->getTracksCollection();

			$trackingInfos=array();
			foreach ($tracks as $track)
            {
				$carrierCode = $track->getCarrierCode();

				if($carrierCode == "colissimo" || $carrierCode == "colissimopostoffice" || $carrierCode == "colissimocityssimo" || $carrierCode == "colissimolocalstore")
                {
                	$carrierModel = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
    				$model = Mage::getModel($carrierModel);

                	$t = Mage::getModel('shipping/tracking_result_status');
	                $t->setNumber($track->getNumber() );
	                $t->setTracking($track->getNumber() );
	                    $t->setUrl($model->getTrackingUrl($track->getNumber()) );
	                    $t->setCarrierTitle($track->getTitle() );
	                    $trackingInfos[] = $t;
				}
                else
                {
					$trackingInfos[] = $track->getNumberDetail();
				}
			}
			$shipTrack[$increment_id] = $trackingInfos;
		}
        return $shipTrack;
    }

    public function getTrackingInfoByShip()
    {
        $shipTrack = array();
        $shipment = $this->_initShipment();
        if ($shipment) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track)
            {
				$carrierCode = $track->getCarrierCode();

				if($carrierCode == "colissimo" || $carrierCode == "colissimopostoffice" || $carrierCode == "colissimocityssimo" || $carrierCode == "colissimolocalstore")
                {
                	$carrierModel = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
    				$model = Mage::getModel($carrierModel);

                	$t = Mage::getModel('shipping/tracking_result_status');
	                $t->setNumber($track->getNumber() );
	                $t->setTracking($track->getNumber() );
	                    $t->setUrl($model->getTrackingUrl($track->getNumber()) );
	                    $t->setCarrierTitle($track->getTitle() );
	                    $trackingInfos[] = $t;
				}
                else
                {
					$trackingInfos[] = $track->getNumberDetail();
				}
			}
            $shipTrack[$increment_id] = $trackingInfos;
        }
        return $shipTrack;
    }

    public function getTrackingInfoByTrackId()
    {
    	$track = Mage::getModel('sales/order_shipment_track')->load($this->getTrackId());
    	$carrierCode = $track->getCarrierCode();
    	if($carrierCode == "colissimo")
        {
    		$carrierModel = Mage::getStoreConfig('carriers/'.$carrierCode.'/model');
    		$model = Mage::getModel($carrierModel);
    		$t = Mage::getModel('shipping/tracking_result_status');
	        $t->setNumber($track->getNumber() );
	        $t->setTracking($track->getNumber() );
	        $t->setUrl($model->getTrackingUrl($track->getNumber()) );
	        $t->setCarrierTitle($track->getTitle() );
	        $shipTrack[] = array($t);
    	}
        else
        {
    		$shipTrack[] = array(Mage::getModel('sales/order_shipment_track')->load($this->getTrackId())
                       ->getNumberDetail());
    	}
        return $shipTrack;
    }

	/**
	 * Returns the Magento's code of shipping method
	 *
	 * @param $method
	 * @return string
	 */
	private function getCodeShippingMethod($method){
		$data = explode('_', $method );
        $carrierCode = $data[0];
        return $carrierCode;
	}

}
