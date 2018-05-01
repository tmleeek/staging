<?php
/**
 * Pinterest Pin button block interface.
 */
interface TBT_Rewardssocial_Block_Pinterest_Pin_Button_Interface
{
    /**
     * Whether the Pin button will have a counter or no.
     * @return boolean
     */
    public function isCounterEnabled();

    public function getRequestUriEncoded();

    public function getPinnableMediaUriEncoded();
}
