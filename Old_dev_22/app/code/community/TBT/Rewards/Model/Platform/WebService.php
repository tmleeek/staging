<?php

/**
 * @method TBT_Rewards_Model_Platform_WebService setClient($client)
 * @method TBT_Rewards_Model_Platform_WebService setApiKey($apiKey)
 * @method TBT_Rewards_Model_Platform_WebService setUser($user)
 * @method TBT_Rewards_Model_Platform_WebService_User getUser()
 */
class TBT_Rewards_Model_Platform_WebService extends Varien_Object
{
    const CONFIG_XPATH_ROLE_ID = 'rewards/platform/soap/role_id';
    const CONFIG_XPATH_USER_ID = 'rewards/platform/soap/user_id';

    public function setup()
    {
        $this->_createUser();
        $this->_createRole();
        $this->_savePlatformChannel();

        return $this;
    }

    public function getApiKey()
    {
        if ($this->getData('api_key')) {
            return $this->getData('api_key');
        }

        $this->setApiKey(md5(time()));
        return $this->getApiKey();
    }

    /**
     * @return TBT_Rewards_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('rewards');
    }

    protected function _createRole()
    {
        /** @var $role Mage_Api_Model_Role */
        /** @var $rules TBT_Rewards_Model_Platform_WebService_Rules */
        $role = Mage::getModel('api/role');
        $user = $this->getUser();

        if ($this->isRoleCreated()) {
            $this->_helper()->log("Role is already created, not going to create again");
            return $this;
        }

        $this->_helper()->log("Creating role");
        $role->setRoleType('G')
            ->setTreeLevel(1)
            ->setRoleName('Sweet Tooth')
            ->save();

        $this->_helper()->log("Associating role to user");
        $user->setRoleIds(array($role->getId()))
            ->setRoleUserId($this->getId())
            ->saveRelations();

        $this->_helper()->log("Creating rule for the role.");
        $rules = Mage::getModel("api/rules");
        $rules->setRoleId($role->getId())
            ->setResources(array("all"))
            ->saveRel();

        Mage::getConfig()->saveConfig(self::CONFIG_XPATH_ROLE_ID, $role->getId());

        return $this;
    }

    protected function _createUser()
    {
        /** @var $user Mage_Api_Model_User */

        $user = $this->loadApiUser();
        if ($user && $user->getId()) {
            throw new Exception("Looks like the API User was created previously, please delete it and connect again");
        }

        $this->_helper()->log("Creating User");

        $user = Mage::getModel('api/user');
        $user = $user->setFirstname('Sweet')
            ->setLastname('Tooth')
            ->setEmail('support@sweettoothrewards.com')
            ->setUsername('sweettooth')
            ->setApiKey($this->getApiKey())
            ->save();

        Mage::getConfig()->saveConfig(self::CONFIG_XPATH_USER_ID, $user->getId());

        $this->setUser($user);
        return $this->getUser();
    }

    /**
     * @return TBT_Rewards_Model_Platform_Instance
     * @throws Exception
     */
    public function getClient()
    {
        if (!$this->getData('client')) {
            throw new Exception("You have to setClient() on this model first");
        }

        return $this->getData('client');
    }

    protected function _savePlatformChannel()
    {
        $client = $this->getClient();
        $user = $this->getUser();

        $client->channel()->update(array(
            'access_key' => $user->getUsername(),
            'access_secret' => $this->getApiKey(),
            'pending_sync' => 1,
        ));

        return $this;
    }

    public function isRoleCreated()
    {
        return Mage::getStoreConfig(self::CONFIG_XPATH_ROLE_ID);
    }

    public function loadApiUser()
    {
        $userId = Mage::getStoreConfig(self::CONFIG_XPATH_USER_ID);
        if (!$userId) {
            return null;
        }

        /** @var $user Mage_Api_Model_User */
        $user = Mage::getModel('api/user');
        $user->load($userId);

        return $user;
    }
}