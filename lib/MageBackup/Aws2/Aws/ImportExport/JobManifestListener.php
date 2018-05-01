<?php
/**
 * Copyright 2010-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace MageBackup\Aws\ImportExport;

use MageBackup\Guzzle\Common\Event;
use MageBackup\Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener used to assist with formatting the Manifest parameter of CreateJob operation into YAML
 */
class JobManifestListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array('command.before_prepare' => array('onCommandBeforePrepare'));
    }

    /**
     * An event handler for assisting with formatting the Manifest parameter of CreateJob operation into YAML
     *
     * @param Event $event The event being handled
     */
    public function onCommandBeforePrepare(Event $event)
    {
        /** @var \MageBackup\Guzzle\Service\Command\AbstractCommand $command */
        $command = $event['command'];
        if ($command->getName() === 'CreateJob') {
            $manifest = $command->get('Manifest');
            if (!is_string($manifest) && class_exists('MageBackup\Symfony\Component\Yaml\Yaml')) {
                $command->set('Manifest', \MageBackup\Symfony\Component\Yaml\Yaml::dump($manifest));
            }
        }
    }
}
