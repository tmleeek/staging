<?php

/**
 * This is the main adapter class designed to interface the Milestone rule module
 * with the tbtrewards_special interface.
 *
 */
class TBT_Milestone_Model_Adapter_Special extends Varien_Object 
{
    /**
     * Listens on tbtrewards_special_controller_save_after event
     * which is triggered as soon as a special rule is saved in tbtrewards.
     * 
     * Creates or updates a Milestone rule.
     *  
     * @param Varien_Event_Observer $observer containing original form data + rewards_special_id
     * @return self
     */
    public function afterSaveAction($observer)
    {   
        try {
            $helper = Mage::helper('tbtmilestone');            
            $specialRule = $observer->getData();
            $rewardsSpecialId = $specialRule['rewards_special_id'];
            if (empty($rewardsSpecialId)){
                return $this;
            }
            
            $milestoneRule = Mage::getModel('tbtmilestone/rule');
            $milestoneRule->load($rewardsSpecialId, 'rewards_special_id');
            
            // If this is not a milestone rule,
            if (strpos($specialRule['points_conditions'], "tbtmilestone") === false){            
                if ($milestoneRule->getId()){
                    $milestoneRule->delete();
                }
                
                return $this;
            }
    
            $conditionType = str_replace ("tbtmilestone_", "", $specialRule['points_conditions']);
            $conditionDetails = array();
            
            $fromDate = $specialRule["from_date"];
            if (empty($fromDate)){
                $fromDate = $helper->getLocalTimestamp();
                $fromDate = $helper->getNormalizedDateString($fromDate);                
            }
            $conditionDetails["from_date"] = $helper->getMySqlDateString($helper->getUtcTimestamp($fromDate));                                                     // use supplied normalized date or current normalized time                   
            $conditionDetails["to_date"]   = empty($specialRule["to_date"]) ? "" : $helper->getMySqlDateString($helper->getUtcTimestamp($specialRule["to_date"])); // use supplied normalized date or nothing           
            if (isset($specialRule[$specialRule['points_conditions']])){
                $conditionDetails["threshold"] = $specialRule[$specialRule['points_conditions']];
            }        
    
            
            $actionType = str_replace("_", "", $specialRule['points_action']);
            $actionDetails = array();
            switch ($actionType){
                case "customergroup":
                    $actionDetails["customer_group_id"] = $specialRule["customer_group_id"];
                    break;
    
                case "grantpoints":
                    $actionDetails["points_amount"] = floor($specialRule["points_amount"]);
                    break;
                    
                default:
                    break;
            }
            
            
            $milestoneRule->setData("rewards_special_id", $specialRule['rewards_special_id']);
            $milestoneRule->setData("name",               $specialRule['name']);
            $milestoneRule->setData("is_enabled",         $specialRule['is_active']);
            $milestoneRule->setData("website_ids",        $specialRule['website_ids']);
            $milestoneRule->setData("customer_group_ids", $specialRule['customer_group_ids']);
                    
            $milestoneRule->setData("condition_type",     $conditionType);
            $milestoneRule->setData("condition_details",  $conditionDetails);
            
            $milestoneRule->setData("action_type",        $actionType);
            $milestoneRule->setData("action_details",     $actionDetails);        
            
                       
            $milestoneRule->save();
            
        } catch (Exception $e){
            // If a special rule was created for this, delete it and rethrow the error
            $specialRule = Mage::getModel('rewards/special')->load($rewardsSpecialId);
            $milestoneRule = Mage::getModel('tbtmilestone/rule')->load($rewardsSpecialId, 'rewards_special_id');            
            if ($specialRule->getId() && !$milestoneRule->getId()){
                $specialRule->delete();
            }
            
            throw $e;
        }
                
        return $this;
    }
    
    /**
     * Listens on tbtrewards_special_controller_edit_before event
     * which is triggered as soon as a special rule is edited in tbtrewards.
     *
     * Prepares the special form with milestone specific rule data.
     * Note that most of the data is already saved inside the special rule as well
     * so we only need the data that's not already there.
     * 
     * The Model used to populate the data is in the registry as "global_manage_special_rule".
     * We'll have to edit that.
     *
     * @param Varien_Event_Observer $observer containing rewards_special_id
     * @return self
     */    
    public function beforeEditAction($observer)
    {
        $helper = Mage::helper('tbtmilestone');
        $specialRule = Mage::registry ( 'global_manage_special_rule' );      
        if (!$specialRule->getId()){
            return $this;
        }
        
        $milestoneRule = Mage::getModel('tbtmilestone/rule');
        $milestoneRule->load($specialRule->getId(), 'rewards_special_id');
        if (!$milestoneRule->getId()){
            return $this;
        }
        
        $conditionCode = $specialRule->getPointsConditions();
        $conditionCode = is_array($conditionCode) ? $conditionCode[0] : $conditionCode;
        if (strpos($conditionCode, "tbtmilestone") === false){  
            return $this;
        }
        
        $condition = $milestoneRule->getCondition();
        
        $threshold = $condition->getThreshold();
        if (!empty($threshold)){
            $specialRule->setData($conditionCode, $threshold);
        }
        
        $fromDate = $condition->getFromDate();
        if (!empty($fromDate)){
            $fromDate = $helper->getLocalTimestamp($fromDate);
            $fromDate = $helper->getNormalizedDateString($fromDate);
            $specialRule->setData('from_date', $fromDate);
        }
        
        $toDate = $condition->getToDate();
        if (!empty($toDate)){
            $toDate = $helper->getLocalTimestamp($toDate);
            $toDate = $helper->getNormalizedDateString($toDate);
            $specialRule->setData('to_date', $toDate);
        }       
        
        
        $action = $milestoneRule->getAction();

        $customerGroupId = $action->getCustomerGroupId();
        if (!empty($customerGroupId)){            
            $specialRule->setData("customer_group_id", $customerGroupId);
        }
    
        return $this;        
    }
} 

