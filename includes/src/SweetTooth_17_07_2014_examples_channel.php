<?php

include_once(dirname(__FILE__).'/../SweetTooth.php');

// PLUG YOUR ACCOUNT INFO IN HERE
$apiKey = '';
$apiSecret = '';
$subdomain = '';

if (!$apiKey || !$apiSecret || !$subdomain) {
    echo "You need to enter the apiKey, apiSecret, and subdomain in channel.php.
        If you don't have an account yet, run account.php to create one.
    ";
    return;
}

// Instanciate new SweetTooth with account credentials
$st = new SweetTooth($apiKey, $apiSecret, $subdomain);

$channelData = array (
    'channel_type' => 'magento'
);

echo "
    <div>
        Creating channel with data:<br/>
        <pre>" . print_r($channelData, true) . "</pre>
    </div>
    <br/>
";

try {
    // Create a magento channel for our new account
    $channel = $st->channel()->create($channelData);
} catch (Exception $e) {
    // Something went wrong!
    echo 'Error creating your channel: ' . $e->getMessage();
    return;
}

// Awesome, your account and channel was created!
$result = "
    <div>
       Channel info:<br/>
       <pre>" . print_r($channel, true) . "</pre>
    </div>
    <br/>
    <b>Next Step: Paste the following into transfer.php to make calls on behalf of this channel,</b>
<pre>
\$apiKey = '" . $channel['api_key'] . "';
\$apiSecret = '" . $channel['api_secret'] . "';
\$subdomain = '" . $subdomain . "';
</pre>
";

echo $result;

?>
