<?php

class Tatva_Shipping_Model_Mysql4_Tempcolissimowebservicedata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the subscription_id refers to the key field in your database table.
        $this->_init('tatvashipping/shipping_temp_store_colissimo_web_service_data', 'id');
    }

    public function deleteOldData($customer_id)
    {
        $write = $this->_getWriteAdapter();
        $conds[] = $write->quoteInto('customer_id', $customer_id);
        $write->delete($this->getTable('tatvashipping/shipping_temp_store_colissimo_web_service_data'), $conds);
        return $this;
    }

    public function getWebservicearr($data,$var_mobility)
    {
        $read = $this->_getReadAdapter();
        $cond = '';       
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if(!empty($data))
        {
            $cond = implode(" OR ", $data);
            $cond = "(".$cond.")";
            if($var_mobility != "")
            {
                $var_mobility = ' AND '.$var_mobility;
            }

        }
        $where = "";
        if(!empty($data) || $var_mobility != '')
        {
            $where = " AND ".$cond.$var_mobility;
        }
        $sql = "select * from tatva_shipping_temp_store_colissimo_web_service_data  where customer_id = ".$customer_id.$where;
        $res = $read->fetchAll($sql);

        return $res;

    }
	
	public function getMondialrelayarr()
    {
      $read = $this->_getReadAdapter();
      $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
      $sql = "select * from tatva_shipping_temp_store_colissimo_web_service_data  where customer_id = ".$customer_id." and type = 'mondial relay'";
        $res = $read->fetchAll($sql);

        return $res;
    }
	
	public function deleteMondialRelayData($customer_id)
    {
        $write = $this->_getWriteAdapter();
        $conds[] = $write->quoteInto('customer_id=?', $customer_id);
        $conds[] = $write->quoteInto('type=?', 'mondial relay');


        $write->delete($this->getTable('tatvashipping/shipping_temp_store_colissimo_web_service_data'), $conds);
        //$write->delete("delete from sqli_shipping_temp_store_colissimo_web_service_data where customer_id = $customer_id");
        return $this;
    }


}

