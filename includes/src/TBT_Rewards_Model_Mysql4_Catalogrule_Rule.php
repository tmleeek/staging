<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql Catalog Rule Rule
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Mysql4_CatalogRule_Rule extends Mage_CatalogRule_Model_Mysql4_Rule
{
    protected $_loadedRules = array();
    
    /**
     * @param   int|string $date
     * @param   int $wId
     * @param   int $gId
     * @return  Zend_Db_Select
     */
    public function getActiveCatalogruleProductsReader($date, $wId, $gId)
    {
        //$read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $read = $this->_getReadAdapter();
        //$catalogrule_price_table = Mage::getConfig()->getTablePrefix() . ;
        $catalogrule_price_table = $this->getTable('rewards/catalogrule_product');

        $select = $read->select()
            ->from(array('p' => $catalogrule_price_table),
                array('product_id', 'rules_hash'))
            ->where('p.rule_date = ?', $date)
            ->where('p.customer_group_id = ?', $gId)
            ->where('p.website_id = ?', $wId)
            ->where('p.rules_hash IS NOT NULL');
        $this->_filterActiveCatalogruleProducts($select, $wId);

        return $select;
    }

    /**
     * @param   int|string $date
     * @param   int $wId
     * @param   int $gId
     * @return  array | false    applicable redemption product_id and rules_hash.
     */
    public function getActiveCatalogruleProducts($date, $wId, $gId)
    {
        $read = $this->_getReadAdapter();
        $select = $this->getActiveCatalogruleProductsReader($date, $wId, $gId);
        return $read->fetchAll($select);
    }

    /**
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select
     */
    protected function _filterActiveCatalogruleProducts(&$select, $websiteId)
    {
        $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
        Mage::getModel('rewards/catalog_product_visibility')->addVisibileFilterToCR($select, $storeId);
        Mage::getModel('rewards/catalog_product_status')->addVisibileFilterToCR($select, $storeId);

        return $this;
    }

    /**
     * @param   int|string $date
     * @param   int $wId
     * @param   int $gId if gId is set as null then filtering on group is skipped
     * @param   int $pId
     * @return  array | false    applicable redemption rules hash.
     */
    public function getApplicableRedemptionRewards($date, $wId, $gId, $pId)
    {
        $date = $this->formatDate($date, false);
        $read = $this->_getReadAdapter();

        $select = $read->select()
            ->from($this->getTable('rewards/catalogrule_product'), 'rules_hash')
            ->where('rule_date = ?', $date)
            ->where('website_id = ?', $wId)
            ->where('product_id = ?', $pId);
        if ($gId !== null) {
            $select->where('customer_group_id = ?', $gId);
        }

        $rulesHash = $read->fetchOne($select);
        if ($rulesHash) {
            $rules = Mage::helper('rewards')->unhashIt($rulesHash);
        } else {
            $rules = array();
        }
        if (!isset($rules['0'])) {
            $rules = array();
        }

        return $rules;
    }

    /**
     * Returns the applicable reward array from the catalog product price table.
     *
     * @param date $date
     * @param int $wId
     * @param int $gId
     * @param int $pId
     * @param int $ruleId
     * @return array | false
     */
    public function getApplicableReward($date, $wId, $gId, $pId, $ruleId)
    {
        $applicableRules = $this->getApplicableRedemptionRewards($date, $wId, $gId, $pId);

        foreach ($applicableRules as &$applicableRule) {
            $applicableRule = (array) $applicableRule;
            if ($applicableRule['rule_id'] == $ruleId) {
                return $applicableRule;
            }
        }
        return false;
    }

    /**
     * Generate product redemption rule hashes for specified date range
     * If from date is not defined - will be used previous day by UTC
     * If to date is not defined - will be used next day by UTC
     * Mimics Mage_CatalogRule_Model_Resource_Rule::applyAllRulesForDateRange() but for points redemption rules.
     * 
     * TODO: split this up into multiple methods
     *
     * @param int $productId
     * @param int|string|null $fromDate
     * @param int|string|null $toDate
     * @return self
     */
    public function applyAllRedemptionRulesForDateRange($productId = null, $fromDate = null, $toDate = null)
    {
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        //Mage::dispatchEvent('catalogrule_before_apply', array('resource' => $this));

        $clearOldData = false;
        if ($fromDate === null) {
            $fromDate = mktime(0, 0, 0, date('m'), date('d') - 1);
            // If fromDate not specified we can delete all data oldest than 1 day
            $clearOldData = true;
        }
        if (is_string($fromDate)) {
            $fromDate = strtotime($fromDate);
        }
        if ($toDate === null) {
            $toDate = mktime(0, 0, 0, date('m'), date('d') + 1);
        }
        if (is_string($toDate)) {
            $toDate = strtotime($toDate);
        }

        $product = null;
        if ($productId instanceof Mage_Catalog_Model_Product) {
            $product = $productId;
            $productId = $productId->getId();
        }

        $this->removeRedemptionRulesForDateRange($fromDate, $toDate, $productId);
        if ($clearOldData) {
            $this->deleteOldRedemptionData($fromDate, $productId);
        }

        $dayPrices = array();
        try {
            // Update products rules hashes per each website separately because of max join limit in mysql
            foreach (Mage::app()->getWebsites(false) as $website) {
                $productsStmt = $this->_getRuleProductsStmt(
                    $fromDate,
                    $toDate,
                    $productId,
                    $website->getId()
                );

                $dayPrices  = array();
                $stopFlags  = array();
                $prevKey    = null;

                while ($ruleData = $productsStmt->fetch()) {
                    $rule = $this->_getCatalogRule($ruleData['rule_id']);
                    if (!$rule) {
                        continue;
                    }

                    if (!$rule->isRedemptionRule()) {
                        continue;
                    }

                    $effect = $rule->getEffect();
                    if (empty($effect)) {
                        continue;
                    }

                    $ruleProductId = $ruleData['product_id'];
                    $productKey = $ruleProductId . '_'
                        . $ruleData['website_id'] . '_'
                        . $ruleData['customer_group_id'];

                    if ($prevKey && ($prevKey != $productKey)) {
                        $stopFlags = array();
                    }

                    // Build hashes for each day
                    for ($time = $fromDate; $time <= $toDate; $time += self::SECONDS_IN_DAY) {
                        if (($ruleData['from_time'] == 0 || $time >= $ruleData['from_time'])
                            && ($ruleData['to_time'] == 0 || $time <=$ruleData['to_time'])
                        ) {
                            $priceKey = $time . '_' . $productKey;

                            if (isset($stopFlags[$priceKey])) {
                                continue;
                            }

                            if (!isset($dayPrices[$priceKey])) {
                                $dayPrices[$priceKey] = array(
                                    'rule_date'         => $time,
                                    'website_id'        => $ruleData['website_id'],
                                    'customer_group_id' => $ruleData['customer_group_id'],
                                    'product_id'        => $ruleProductId,
                                    'rules_hash'        => $this->_generateRuleProductRedemptionHash($rule),
                                    'latest_start_date' => $ruleData['from_time'],
                                    'earliest_end_date' => $ruleData['to_time']
                                );
                            } else {
                                $dayPrices[$priceKey]['rules_hash'] = $this->_generateRuleProductRedemptionHash(
                                    $rule,
                                    $dayPrices[$priceKey]
                                );
                                $dayPrices[$priceKey]['latest_start_date'] = max(
                                    $dayPrices[$priceKey]['latest_start_date'],
                                    $ruleData['from_time']
                                );
                                $dayPrices[$priceKey]['earliest_end_date'] = min(
                                    $dayPrices[$priceKey]['earliest_end_date'],
                                    $ruleData['to_time']
                                );
                            }

                            if ($ruleData['action_stop']) {
                                $stopFlags[$priceKey] = true;
                            }
                        }
                    }

                    $prevKey = $productKey;
                    if (count($dayPrices) > 1000) {
                        $this->_saveRuleProductRedemptionHash($dayPrices);
                        $dayPrices = array();
                    }
                }
                $this->_saveRuleProductRedemptionHash($dayPrices);
            }
            $this->_saveRuleProductRedemptionHash($dayPrices);

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        // TODO: dispatch an appropriate event here
//         Mage::dispatchEvent('catalogrule_after_apply', array(
//             'product' => $product,
//             'product_condition' => $productCondition
//         ));

        return $this;
    }

    /**
     * Remove product redemption rule hashes for specified date range and product.
     * Mimics Mage_CatalogRule_Model_Resource_Rule::removeCatalogPricesForDateRange() but for points redemption rules.
     *
     * @param int|string $fromDate
     * @param int|string $toDate
     * @param int|null $productId
     * @return self
     */
    public function removeRedemptionRulesForDateRange($fromDate, $toDate, $productId = null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond . ' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id = ?', $productId);
        }

        $write->delete($this->getTable('rewards/catalogrule_product'), $conds);
        return $this;
    }

    /**
     * Delete old product redemption rule hash data
     * Mimics Mage_CatalogRule_Model_Resource_Rule::deleteOldData() but for points redemption rules.
     *
     * @param unknown_type $date
     * @param mixed $productId
     * @return self
     */
    public function deleteOldRedemptionData($date, $productId = null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $conds[] = $write->quoteInto('rule_date < ?', $this->formatDate($date));
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id = ?', $productId);
        }
        $write->delete($this->getTable('rewards/catalogrule_product'), $conds);
        return $this;
    }

    /**
     * Save redemption rule hashes for products to DB
     * Mimics Mage_CatalogRule_Model_Resource_Rule::_saveRuleProductPrices() but for points redemption rules.
     *
     * @param array $arrData
     * @return self
     */
    protected function _saveRuleProductRedemptionHash($arrData)
    {
        if (empty($arrData)) {
            return $this;
        }

        foreach ($arrData as $key => $data) {
            $arrData[$key]['rule_date']          = $this->formatDate($data['rule_date'], false);
            $arrData[$key]['latest_start_date']  = $this->formatDate($data['latest_start_date'], false);
            $arrData[$key]['earliest_end_date']  = $this->formatDate($data['earliest_end_date'], false);
        }

        $this->_getWriteAdapter()->insertOnDuplicate($this->getTable('rewards/catalogrule_product'), $arrData);
        return $this;
    }

    /**
     * Generates a redemption rule hash based on a rule object and a pre-existing rule hash (if one is given).
     * Mimics Mage_CatalogRule_Model_Resource_Rule::_calcRuleProductPrice() but for points redemption rules.
     *
     * @param array $ruleData
     * @param null|array $productData
     * @return string
     */
    protected function _generateRuleProductRedemptionHash($rule, $productData = null)
    {
        $rulesHash = array();
        if ($productData !== null && isset($productData['rules_hash'])) {
            $rulesHash = json_decode(base64_decode($productData['rules_hash']));
        }

        $rulesHash[] = $rule->getHashEntry();

        return base64_encode(json_encode($rulesHash));
    }

    /**
     * Returns a rule and makes sure rules are only ever loaded once
     *
     * @param integer $ruleId
     * @return self
     */
    protected function _getCatalogRule($ruleId)
    {
        if (isset($this->_loadedRules[$ruleId])) {
            return $this->_loadedRules[$ruleId];
        }

        $rule = Mage::getModel('rewards/catalogrule_rule')->load($ruleId);
        $this->_loadedRules[$ruleId] = $rule;

        return $rule;
    }
}
