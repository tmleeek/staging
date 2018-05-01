<?php

/**
 * This script will update newly added 'source_reference_id' column in table 'rewards_transfer' to contain value of
 * the main reference ID that generated this transfer (the source reference ID)
 */
$this->attemptQuery("
    UPDATE `{$this->getTable('rewards/transfer')}` AS t1
              INNER JOIN (
                SELECT `rewards_transfer_id`, `rewards_transfer_reference_id`
                FROM `{$this->getTable('rewards/transfer_reference')}`
                GROUP BY `rewards_transfer_id`
              ) AS t2
              ON t1.`rewards_transfer_id` = t2.`rewards_transfer_id`
    SET t1.`source_reference_id` = t2.`rewards_transfer_reference_id`
");