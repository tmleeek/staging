<?php

class Tatva_Shipping_Model_Mysql4_Tempcolissimowebservicedatasearchresult extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the subscription_id refers to the key field in your database table.
        $this->_init('tatvashipping/shipping_temp_store_colissimo_web_service_data_searchresult', 'id');
    }

    public function deleteOldData($customer_id)
    {
        $write = $this->_getWriteAdapter();
        $conds[] = $write->quoteInto('customer_id', $customer_id);
        $write->delete($this->getTable('tatvashipping/shipping_temp_store_colissimo_web_service_data_searchresult'), $conds);
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
        $sql = "select * from tatva_shpp_temp_store_colissimo_web_service_srchresult where customer_id = ".$customer_id.$where;
        $res = $read->fetchAll($sql);

        return $res;

    }


}

