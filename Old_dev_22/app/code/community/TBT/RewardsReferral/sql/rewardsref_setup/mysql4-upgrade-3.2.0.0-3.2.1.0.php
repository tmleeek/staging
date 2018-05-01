<?php

$installer = $this;

$installer->startSetup();

$installer->attemptQuery("
    UPDATE `rewardsref_referral` SET
    `referral_status` = CASE
    WHEN `referral_status` = 1 then 2
    WHEN `referral_status` = 2 then 3
    WHEN `referral_status` = 3 then 4
    WHEN `referral_status` = 4 then 1
    ELSE `referral_status`
    END
");

$installer->endSetup();
