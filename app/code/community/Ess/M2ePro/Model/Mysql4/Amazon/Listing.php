<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Mysql4_Amazon_Listing
    extends Ess_M2ePro_Model_Mysql4_Component_Child_Abstract
{
    protected $_isPkAutoIncrement = false;

    //########################################

    public function _construct()
    {
        $this->_init('M2ePro/Amazon_Listing', 'listing_id');
        $this->_isPkAutoIncrement = false;
    }

    //########################################

    public function updateStatisticColumns()
    {
        $this->updateStatisticCountColumns();

        $listingTable = Mage::getResourceModel('M2ePro/Listing')->getMainTable();
        $listingProductTable = Mage::getResourceModel('M2ePro/Listing_Product')->getMainTable();
        $amazonListingProductTable = Mage::getResourceModel('M2ePro/Amazon_Listing_Product')->getMainTable();

        $select = $this->_getReadAdapter()
                       ->select()
                       ->from(
                            array('lp' => $listingProductTable),
                            new Zend_Db_Expr('SUM(`online_qty`)')
                       )
                       ->join(
                            array('alp' => $amazonListingProductTable),
                            'lp.id = alp.listing_product_id',
                            array()
                       )
                       ->where("`listing_id` = `{$listingTable}`.`id`")
                       ->where("`status` = ?",(int)Ess_M2ePro_Model_Listing_Product::STATUS_LISTED);

        $query = "UPDATE `{$listingTable}`
                  SET `items_active_count` =  IFNULL((".$select->__toString()."),0)
                  WHERE `component_mode` = '".Ess_M2ePro_Helper_Component_Amazon::NICK."'";

        $this->_getWriteAdapter()->query($query);
    }

    private function updateStatisticCountColumns()
    {
        $listingTable = Mage::getResourceModel('M2ePro/Listing')->getMainTable();
        $listingProductTable = Mage::getResourceModel('M2ePro/Listing_Product')->getMainTable();
        $amazonListingProductTable = Mage::getResourceModel('M2ePro/Amazon_Listing_Product')->getMainTable();

        $statisticsData = array();
        $statusListed = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;

        $totalCountSelect = $this->_getReadAdapter()
            ->select()
            ->from(
                array('lp' => $listingProductTable),
                array(
                    'listing_id' => 'listing_id',
                    'count'      => new Zend_Db_Expr('COUNT(*)')
                )
            )
            ->join(
                array('alp' => $amazonListingProductTable),
                'lp.id = alp.listing_product_id',
                array()
            )
            ->where("`variation_parent_id` IS NULL")
            ->group('listing_id')
            ->query();

        while ($row = $totalCountSelect->fetch()) {

            if (empty($row['listing_id'])) {
                continue;
            }

            $statisticsData[$row['listing_id']] = array(
                'total'    => (int)$row['count'],
                'active'   => 0,
                'inactive' => 0
            );
        }

        $activeCountSelect = $this->_getReadAdapter()
            ->select()
            ->from(
                array('lp' => $listingProductTable),
                array(
                    'listing_id' => 'listing_id',
                    'count'      => new Zend_Db_Expr('COUNT(*)')
                )
            )
            ->join(
                array('alp' => $amazonListingProductTable),
                'lp.id = alp.listing_product_id',
                array()
            )
            ->where("`variation_parent_id` IS NULL")
            ->where("lp.status = {$statusListed} OR
                    (alp.is_variation_parent = 1 AND alp.variation_child_statuses REGEXP '\"{$statusListed}\":[^0]')")
            ->group('listing_id')
            ->query();

        while ($row = $activeCountSelect->fetch()) {

            if (empty($row['listing_id'])) {
                continue;
            }

            $total = $statisticsData[$row['listing_id']]['total'];

            $statisticsData[$row['listing_id']]['active']   = (int)$row['count'];
            $statisticsData[$row['listing_id']]['inactive'] = $total - (int)$row['count'];
        }

        $existedListings = $this->_getReadAdapter()
             ->select()
             ->from(
                 array('l' => $listingTable),
                 array('id' => 'id')
             )
             ->where('component_mode = ?', Ess_M2ePro_Helper_Component_Amazon::NICK)
             ->query();

        while ($listingId = $existedListings->fetchColumn()) {

            $totalCount    = isset($statisticsData[$listingId]) ? $statisticsData[$listingId]['total'] : 0;
            $activeCount   = isset($statisticsData[$listingId]) ? $statisticsData[$listingId]['active'] : 0;
            $inactiveCount = isset($statisticsData[$listingId]) ? $statisticsData[$listingId]['inactive'] : 0;

            $query = "UPDATE `{$listingTable}`
                      SET `products_total_count` = {$totalCount},
                          `products_active_count` = {$activeCount},
                          `products_inactive_count` = {$inactiveCount}
                      WHERE `id` = {$listingId}";

            $this->_getWriteAdapter()->query($query);
        }
    }

    //########################################

    public function setSynchStatusNeed($newData, $oldData, $listingProducts)
    {
        $this->setSynchStatusNeedByListing($newData,$oldData,$listingProducts);
        $this->setSynchStatusNeedBySellingFormatTemplate($newData,$oldData,$listingProducts);
        $this->setSynchStatusNeedBySynchronizationTemplate($newData,$oldData,$listingProducts);
    }

    // ---------------------------------------

    public function setSynchStatusNeedByListing($newData, $oldData, $listingsProducts)
    {
        $listingsProductsIds = array();
        foreach ($listingsProducts as $listingProduct) {
            $listingsProductsIds[] = (int)$listingProduct['id'];
        }

        if (empty($listingsProductsIds)) {
            return;
        }

        unset(
            $newData['template_selling_format_id'], $oldData['template_selling_format_id'],
            $newData['template_synchronization_id'], $oldData['template_synchronization_id']
        );

        if (!$this->isDifferent($newData,$oldData)) {
            return;
        }

        $templates = array('listing');

        $this->_getWriteAdapter()->update(
            Mage::getSingleton('core/resource')->getTableName('M2ePro/Listing_Product'),
            array(
                'synch_status' => Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_NEED,
                'synch_reasons' => new Zend_Db_Expr(
                    "IF(synch_reasons IS NULL,
                        '".implode(',',$templates)."',
                        CONCAT(synch_reasons,'".','.implode(',',$templates)."')
                    )"
                )
            ),
            array('id IN ('.implode(',', $listingsProductsIds).')')
        );
    }

    public function setSynchStatusNeedBySellingFormatTemplate($newData, $oldData, $listingsProducts)
    {
        $newSellingFormatTemplate = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
            'Template_SellingFormat', $newData['template_selling_format_id'], NULL, array('template')
        );

        $oldSellingFormatTemplate = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
            'Template_SellingFormat', $oldData['template_selling_format_id'], NULL, array('template')
        );

        Mage::getResourceModel('M2ePro/Amazon_Template_SellingFormat')->setSynchStatusNeed(
            $newSellingFormatTemplate->getDataSnapshot(),
            $oldSellingFormatTemplate->getDataSnapshot(),
            $listingsProducts
        );
    }

    public function setSynchStatusNeedBySynchronizationTemplate($newData, $oldData, $listingsProducts)
    {
        $newSynchTemplate = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
            'Template_Synchronization', $newData['template_synchronization_id'], NULL, array('template')
        );

        $oldSynchTemplate = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
            'Template_Synchronization', $oldData['template_synchronization_id'], NULL, array('template')
        );

        Mage::getResourceModel('M2ePro/Amazon_Template_Synchronization')->setSynchStatusNeed(
            $newSynchTemplate->getDataSnapshot(),
            $oldSynchTemplate->getDataSnapshot(),
            $listingsProducts
        );
    }

    // ---------------------------------------

    public function isDifferent($newData, $oldData)
    {
        $ignoreFields = array(
            $this->getIdFieldName(),
            'id', 'title',
            'component_mode',
            'create_date', 'update_date'
        );

        foreach ($ignoreFields as $ignoreField) {
            unset($newData[$ignoreField],$oldData[$ignoreField]);
        }

        return (count(array_diff_assoc($newData,$oldData)) > 0);
    }

    //########################################
}