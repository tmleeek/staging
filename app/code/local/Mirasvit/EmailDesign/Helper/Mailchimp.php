<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Helper_Mailchimp extends Mage_Core_Helper_Abstract
{
    public function convert($mailchimp)
    {
        $vars = array(
            '*|MC:SUBJECT|*'                  => '{{var subject}}',
            '*|ARCHIVE|*'                     => '{{var url_in_browser}}',
            '*|CURRENT_YEAR|*'                => '{{var current_year}}',
            '*|UNSUB|*'                       => '{{var url_unsubscribe}}',
            '*|FACEBOOK:PROFILEURL|*'         => '{{var facebook_url}}',
            '*|TWITTER:PROFILEURL|*'          => '{{var twitter_url}}',
            '*|LIST:COMPANY|*'                => '{{var store.getFrontendName()}}',
            '*|LIST:DESCRIPTION|*'            => '',
            '*|HTML:LIST_ADDRESS_HTML|*'      => 'outgoiing@email.address',
            '*|IF:REWARDS|* *|HTML:REWARDS|*' => '',
            '*|FORWARD|*'                     => '{{var forward_url}}',
            '/*@editable*/'                   => '',
            '*|UPDATE_PROFILE|*'              => '',
            'mc:repeatable'                   => '',
            'mc:allowtext'                    => '',
            'mc:hideable'                     => '',
            'mc:allowdesigner'                => '',
            '*|IFNOT:ARCHIVE_PAGE|*'          => '',
            '*|END:IF|*'                      => '',
            'mc:edit'                         => 'mcedit',
        );

        foreach ($vars as $old => $new) {
            $mailchimp = str_replace($old, $new, $mailchimp);
        }

        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        @$dom->loadHTML($mailchimp);
        $xpath = new DomXpath($dom);
        $items = $xpath->query('//*[@mcedit]');
        foreach ($items as $mcedit) {
            $area = 'area.'.$mcedit->getAttribute('mcedit');

            if ($area == 'area.monkeyrewards') {
                $mcedit->parentNode->removeChild($mcedit);
                continue;
            }

            $mcedit->removeAttribute('mcedit');
            $a = $dom->createComment('{{ivar '.$area.'}}');
            $b = $dom->createComment('{{/ivar}}');
            $mcedit->parentNode->insertBefore($a, $mcedit);
            $mcedit->parentNode->insertBefore($b);
        }
        $dom->formatOutput = true;
        $dom->preserveWhitespace = false;
        
        $html = $dom->saveHTML();

        $html = str_replace('%7B', '{', $html);
        $html = str_replace('%7D', '}', $html);
        $html = str_replace('%20', ' ', $html);

        $html = preg_replace('/mc:label="[a-zA-Z0-9_]*"/', '', $html);
        $html = preg_replace('/mc:variant="[^"]*"/', '', $html);

        if (strpos($html, 'mc:') !== false) {
            echo htmlspecialchars($html);
            die();
        }
        return $html;
    }
}