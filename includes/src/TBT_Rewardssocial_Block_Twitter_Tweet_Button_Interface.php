<?php
/**
 * Tweet button interface.
 */
interface TBT_Rewardssocial_Block_Twitter_Tweet_Button_Interface
{
    /**
     * Return the URL that will be tweeted. If null specified, the curent page's URL will be tweeted. Especially needed,
     * when multiple Tweet buttons are present on the same page and we want to display counts for each.
     * @return string Represents the URL to be tweeted.
     */
    public function getTweetedUrl();

    /**
     * Whether the Tweet button will have a counter or no.
     * @return boolean
     */
    public function isCounterEnabled();

    /**
     * Return the tweeted message
     * @return string
     */
    public function getTweet();
}