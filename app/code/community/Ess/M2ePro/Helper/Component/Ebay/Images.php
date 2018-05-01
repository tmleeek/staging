<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Helper_Component_Ebay_Images extends Mage_Core_Helper_Abstract
{
    //########################################

    /**
     * @param Ess_M2ePro_Model_Magento_Product_Image[] $images
     * @return string $hash
     */
    public function getHash(array $images)
    {
        if (empty($images)) {
            return null;
        }

        $hashes = array();
        $haveNotSelfHostedImage = false;

        foreach($images as $image) {

            $tempImageHash = $image->getHash();

            if (!$image->isSelfHosted()) {
                $haveNotSelfHostedImage = true;
            }

            $hashes[] = $tempImageHash;
        }

        $hash = md5(Mage::helper('M2ePro')->jsonEncode($hashes));

        if ($haveNotSelfHostedImage) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $hash .= '##' . $date->getTimestamp();
        }
        return $hash;
    }

    /**
     * @param string $hash
     * @param int $lifetime (in days) 2 by default
     * @return bool
     */
    public function isHashBelated($hash, $lifetime = 2)
    {
        if (strpos($hash, '##') === false) {
            return false;
        }

        $parts = explode('##', $hash);

        if (empty($parts[1])) {
            return true;
        }

        $validTill = new \DateTime('now', new \DateTimeZone('UTC'));
        $validTill->setTimestamp((int)$parts[1]);
        $validTill->modify("+ {$lifetime} days");

        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        return $now->getTimestamp() >= $validTill->getTimestamp();
    }

    //----------------------------------------

    /**
     * @param string $savedHash
     * @param string $currentHash
     * @return bool
     */
    public function areHashesTheSame($savedHash, $currentHash)
    {
        if ($savedHash == $currentHash) {
            return true;
        }

        if (strpos($savedHash, '##') === false || strpos($currentHash, '##') === false) {
            return false;
        }

        $savedHash = explode('##', $savedHash);
        $currentHash = explode('##', $currentHash);

        return $savedHash[0] == $currentHash[0];
    }

    //########################################
}