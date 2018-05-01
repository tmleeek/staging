<?php

/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 * https://www.sweettoothrewards.com/terms-of-service
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
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Observer_Cron extends Varien_Object {

	/* TODO WDCA - Change the classname and path of this to suit the event being observed */

	public function __construct() {

	}

	/*
     * @warning running this function twice in one day could result in notifying the customer twice.
     */

	public function checkPointsExpiry($observer)
    {
        $time = time();
        //Check if the cron is called twice within 24hours
        if($this->_canSendExpiryEmail($time)){
            //Send the expiry emails
            Mage::getSingleton ( 'rewards/expiry' )->checkAllCustomers ();
            //Email sending is done , store what time is it now
            $this->_writeTimestamp($time);
        }else{
            Mage::helper('rewards')->log("Unable to send Points Expiry Emails. The cron executed less than 24 hours ago.");
        }

		return $this;
	}

    /*
     *  Method to check if the Points expiry cron is called twice within a day.
     *  @param $currnetTimestamp
     *  @return boolean
     */
    protected function _canSendExpiryEmail($currnetTimestamp)
    {

        $mailTimeStamp = $this->_readTimestamp();
        if ($mailTimeStamp == null || $mailTimeStamp == false) {
            //this is the first time or the log file was cleared.
           return true;
        }

        $numDays = round(abs($mailTimeStamp-$currnetTimestamp)/60/60/24);
        if ($numDays>1) {
            return true;
        }

        return false;
    }

    /*
     * Save the timestamp to a file var/log/rewards.expire.email.cron.log
     * Ideally the file will be created once , and read once every day updated once every day.
     *
     * @param $currnetTimestamp
     * return current object
     */
    protected function _writeTimestamp($currnetTimestamp)
    {
        try{
                $fileIO = new Varien_Io_File();
                $fileIO->open(array('path'=>Mage::getBaseDir('var').DS."log"));
                $fileIO->streamOpen(Mage::getBaseDir('var').DS."log".DS.'rewards.expire.email.cron.log', 'w+');
                $fileIO->streamWrite($currnetTimestamp,"w");
                $fileIO->close();
        }catch(Exception $e){
            Mage::helper('rewards')->log($e->getMessage());
        }

       return $this;
    }

    /*
     *  Read the timestamp in the file var/log/rewards.expire.email.cron.log
     *
     *  $return string object
     */
    private function _readTimestamp()
    {
        $content = null;
        try{

            $fileIO = new Varien_Io_File();
            $fileIO->open(array('path'=>Mage::getBaseDir('var').DS."log"));
            $fileIO->streamOpen(Mage::getBaseDir('var').DS."log".DS.'rewards.expire.email.cron.log', 'r');
            $content = $fileIO->streamRead();
            $fileIO->close();

            return $content;
        }catch(Exception $e){
            	Mage::helper('rewards')->log($e->getMessage());
        }

        return $content;
    }

    public function checkPointsProbation($observer)
    {
        // get collection of all Pending-Time transfers
        $transfers = Mage::getModel('rewards/transfer')->getCollection()
            ->addFilter('status', TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME);
        foreach ($transfers as $transfer) {
            // check each transfer if it is time to vest
            if (time() >= strtotime($transfer->getEffectiveStart())) {
                // ask dependent modules if it is safe to vest the transfer (default to yes)
                $result = new Varien_Object(array(
                    'is_safe_to_approve' => true
                ));
                Mage::dispatchEvent('rewards_transfer_vestation', array(
                    'transfer' => $transfer,
                    'result'   => $result,
                ));

                // approve or cancel transfer, based on dependent modules' feedback
                if ($result->getIsSafeToApprove()) {
                    $transfer->setStatus(
                            $transfer->getStatus(),
                            TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED)
                        ->save();
                } else {
                    $transfer->setStatus(
                            $transfer->getStatus(),
                            TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED)
                        ->save();
                }
            }
        }

        return $this;
    }

    public function cfu() {
        if(!Mage::getStoreConfigFlag('rewards/general/cfu')) {
            return $this;
        }

        Mage::helper ( 'rewards/loyalty' )->checkForUpdates ( );

        return $this;
    }

	public function cronTest($observer) {
		return $this;
	}


}