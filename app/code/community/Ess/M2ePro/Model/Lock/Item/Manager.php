<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Lock_Item_Manager extends Varien_Object
{
    private $nick = 'undefined';
    private $maxInactiveTime = 1800; // 30 min

    /** @var Ess_M2ePro_Model_Lock_Item */
    private $lockModel;

    //########################################

    public function setLockModel(Ess_M2ePro_Model_Lock_Item $lock)
    {
        $this->lockModel = $lock;
        $this->nick = $lock->getNick();

        return $this;
    }

    public function getLockModel($reload = true)
    {
        if (is_null($this->lockModel) || $reload) {
            $this->lockModel = Mage::getModel('M2ePro/Lock_Item')->load($this->nick, 'nick');
        }

        return $this->lockModel;
    }

    //########################################

    public function setNick($value)
    {
        $this->nick = $value;
    }

    public function getNick()
    {
        return $this->nick;
    }

    // ---------------------------------------

    public function setMaxInactiveTime($value)
    {
        $this->maxInactiveTime = (int)$value;
    }

    public function getMaxInactiveTime()
    {
        return $this->maxInactiveTime;
    }

    //########################################

    public function create($parentId = NULL)
    {
        $data = array(
            'nick'      => $this->nick,
            'parent_id' => $parentId,
        );

        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = Mage::getModel('M2ePro/Lock_Item')->setData($data);
        $lockModel->save();

        $this->setLockModel($lockModel);
        return true;
    }

    public function remove($reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return false;
        }

        $childrenCollection = Mage::getModel('M2ePro/Lock_Item')->getCollection();
        $childrenCollection->addFieldToFilter('parent_id', $lockModel->getId());

        foreach ($childrenCollection->getItems() as $childLockModel) {

            /** @var $childManager Ess_M2ePro_Model_Lock_Item_Manager **/
            $childManager = Mage::getModel('M2ePro/Lock_Item_Manager');
            $childManager->setLockModel($childLockModel);
            $childManager->remove(false);
        }

        $lockModel->delete();
        return true;
    }

    // ---------------------------------------

    public function isExist($reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return false;
        }

        $currentTimestamp = Mage::helper('M2ePro')->getCurrentGmtDate(true);
        $updateTimestamp  = strtotime($lockModel->getUpdateDate());

        if ($updateTimestamp < $currentTimestamp - $this->getMaxInactiveTime()) {

            Mage::helper('M2ePro/Module_Logger')->process(
                $lockModel->getData(), 'Lock Item was removed by lifetime', false
            );
            $this->remove($reload);
            return false;
        }

        return true;
    }

    public function activate($reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return false;
        }

        if (!is_null($lockModel->getParentId())) {

            /** @var Ess_M2ePro_Model_Lock_Item $parentLockItem */
            $parentLockItem = Mage::getModel('M2ePro/Lock_Item')->load($lockModel->getParentId());

            /** @var $parentManager Ess_M2ePro_Model_Lock_Item_Manager **/
            $parentManager = Mage::getModel('M2ePro/Lock_Item_Manager');
            $parentManager->setLockModel($parentLockItem);
            $parentManager->activate(false);
        }

        if ($lockModel->isKillNow()) {
            $this->remove($reload);
            exit('kill now.');
        }

        $lockModel->setData('data', $lockModel->getContentData());
        $lockModel->setDataChanges(true);
        $lockModel->save();

        return true;
    }

    //########################################

    public function getRealId($reload = true)
    {
        return $this->getLockModel($reload)->getId();
    }

    // ---------------------------------------

    public function addContentData($key, $value, $reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return false;
        }

        $data = $lockModel->getContentData();
        if (!empty($data)) {
            $data = Mage::helper('M2ePro')->jsonDecode($data);
        } else {
            $data = array();
        }

        $data[$key] = $value;

        $lockModel->setData('data', Mage::helper('M2ePro')->jsonEncode($data));
        $lockModel->save();

        return true;
    }

    public function setContentData(array $data, $reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return false;
        }

        $lockModel->setData('data', Mage::helper('M2ePro')->jsonEncode($data));
        $lockModel->save();

        return true;
    }

    // ---------------------------------------

    public function getContentData($key = NULL, $reload = true)
    {
        /** @var $lockModel Ess_M2ePro_Model_Lock_Item **/
        $lockModel = $this->getLockModel($reload);
        if (!$lockModel->getId()) {
            return NULL;
        }

        if ($lockModel->getData('data') == '') {
            return NULL;
        }

        $data = Mage::helper('M2ePro')->jsonDecode($lockModel->getContentData());
        if (is_null($key)) {
            return $data;
        }

        if (isset($data[$key])) {
            return $data[$key];
        }

        return NULL;
    }

    //########################################

    public function makeShutdownFunction()
    {
        if (!$this->isExist()) {
            return false;
        }

        $functionCode = "\$manager = Mage::getModel('M2ePro/Lock_Item_Manager');
                         \$manager->setNick('".$this->nick."');
                         \$manager->remove();";

        $shutdownDeleteFunction = create_function('', $functionCode);
        register_shutdown_function($shutdownDeleteFunction);

        return true;
    }

    //########################################
}