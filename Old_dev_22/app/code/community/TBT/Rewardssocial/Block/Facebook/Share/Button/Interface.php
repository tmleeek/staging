<?php
/**
 * Facebook Share button interface
 */
interface TBT_Rewardssocial_Block_Facebook_Share_Button_Interface
{
    /**
     * FB Share button JS onClick action to be performed when the button is clicked. Currently there isn't an all
     * purpose onClick function handler, so if a different share button is needed you'll have to implement it and call
     * it here.
     * @return string
     */
    public function getOnClickAction();
}