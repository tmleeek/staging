<?php

$installer = $this;
$installer->startSetup();

// Create Twitter tweet table
$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/twitter_tweet')}` (
        `twitter_tweet_id` smallint(11) unsigned NOT NULL auto_increment,
        `customer_id` int(10) unsigned NOT NULL,
        `url` varchar(255) NOT NULL default '',
        `tweet_id` varchar(32) NOT NULL default '',
        `twitter_user_id` varchar(64) NOT NULL default '',
  		`created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (`twitter_tweet_id`),
        KEY `IDX_CUSTOMER` (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Twitter Tweets';
");

// Create foreign key constraint for customer_id
$installer->getConnection()->addConstraint(
    "FK_TWITTER_TWEET_CUSTOMER",
    $this->getTable('rewardssocial/twitter_tweet'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$installer->endSetup();

