<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

set_time_limit(0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

$filePath = './' . $_GET['file'];
$zipObject = new ZipArchive();

if(!$zipObject->open($filePath)) {
    echo 'Unable to open file.';
    return;
}

$zipObject->extractTo('.');
$zipObject->close();

exit('ok');