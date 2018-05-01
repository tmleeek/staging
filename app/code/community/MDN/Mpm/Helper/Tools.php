<?php

class MDN_Mpm_Helper_Tools extends Mage_Core_Helper_Abstract {

    public function truncateText($txt, $max)
    {
        if (strlen($txt) > $max)
        {
            $txt = substr($txt, 0, $max).'...';
        }
        return $txt;
    }

}