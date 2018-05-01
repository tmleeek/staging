<?php

class TBT_Rewards_Model_Admin_Output_Observer extends Varien_Object
{

    /***
     * Handle rewards module "Disable modules output" settings
     * accroding to TBT_Rewards
    */
    public function handleModuleDisabledOutput()
    {
        $request = Mage::app()->getRequest();
        $groups = $request->getParam('groups');

        if(empty($groups)) {
            return $this;
        }

        $configDataModel = Mage::getModel("adminhtml/config_data");

        $section = $request->getParam('section');
        $website = $request->getParam('website');
        $store   = $request->getParam('store');

        $configDataModel->setSection($section)
                        ->setWebsite($website)
                        ->setStore($store);

        // Check value
        $fieldValue = isset($groups["modules_disable_output"]["fields"]["TBT_Rewards"]["value"])
                        ? $groups["modules_disable_output"]["fields"]["TBT_Rewards"]["value"]
                        : null;
         // we're using this to check whether during config save value of TBT_Rewards changed or not
        $rewardsLayoutValue = !Mage::getStoreConfig('rewards/general/layoutsactive', $store);
        // only if TBT_Rewards modules_disable_output value changed force all modules to change accordingly
        if (isset($fieldValue) && $fieldValue != $rewardsLayoutValue) {
            $realTBTRewardsValue = array("value" => $fieldValue);
            if ($fieldValue == 1) {
                $value = array("value" => 0);
                return $this->saveConfigData($configDataModel, $value, $realTBTRewardsValue);
            } else {
                $value = array("value" => 1);
                return $this->saveConfigData($configDataModel, $value, $realTBTRewardsValue);
            }
        }

        return $this;
    }

    /**
     *  Save settings according to TBT_Rewards setting value
     *
     *  @param Mage_Adminhtml_Model_Config_Data $configDataModel
     *  @param Array $value
     *  @param Array $realTBTRewardsValue
     *
     *  @return TBT_Rewards_Model_Admin_Output_Observer
     */
    protected function saveConfigData(Mage_Adminhtml_Model_Config_Data $configDataModel, $value = array(), $realTBTRewardsValue = array())
    {
        try{
            $this->changeRewardsDisableModuleOutputSetting($configDataModel, $realTBTRewardsValue);
            $this->switchLayoutOutputSetting($configDataModel, $value);
            Mage::getConfig()->cleanCache();
            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();
        } catch (Exception $e) {
            Mage::helper('rewards')->logException($e);
        }

        return $this;
    }

    /**
     *  Change "Disable modules output" settings values in rewards modules
     *
     *  @param Mage_Adminhtml_Model_Config_Data $configDataModel
     *  @param Array $realTBTRewardsValue
     *
     *  @return TBT_Rewards_Model_Admin_Output_Observer
     */
    public function changeRewardsDisableModuleOutputSetting(Mage_Adminhtml_Model_Config_Data $configDataModel, $realTBTRewardsValue = array())
    {
        $tbtModules = $this->getTBTModules();

        foreach ($tbtModules as  $moduleName) {
            $moduleGroups = array("modules_disable_output" => array("fields" => array($moduleName => $realTBTRewardsValue)));
            $configDataModel->setGroups($moduleGroups);
            $configDataModel->save();
        }

        return $this;
    }

    /**
     *  Change setting of config value, which controll output, when disable rewards
     *  module from admin
     *
     *  Ex: when TBT_Rewards disabled from admin, then it should show default output
     *      which generate by magento
     *
     *  @param Mage_Adminhtml_Model_Config_Data $configDataModel
     *  @param Array $value
     *
     *  @return TBT_Rewards_Model_Admin_Output_Observer
     */
    public function switchLayoutOutputSetting(Mage_Adminhtml_Model_Config_Data $configDataModel, $value = array())
    {
        $groups = array("general" => array("fields" => array("layoutsactive" => $value)));
        $configDataModel->setGroups($groups);
        $configDataModel->setSection("rewards");
        $configDataModel->save();

        return $this;
    }

    public function getTBTModules()
    {
        return array(
            "TBT_RewardsApi",
            "TBT_RewardsCoreCustomer",
            "TBT_RewardsCoreSpending",
            "TBT_RewardsLoyalty",
            "TBT_RewardsOnly",
            "TBT_RewardsCoreSpending",
            "TBT_RewardsPlat",
            "TBT_RewardsReferral",
            "TBT_Rewardssocial",
            "TBT_Testsweet"
        );
    }
}