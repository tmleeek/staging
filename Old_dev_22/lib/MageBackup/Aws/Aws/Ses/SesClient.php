<?php
namespace MageBackup\Aws\Ses;

use MageBackup\Aws\Credentials\CredentialsInterface;

/**
 * This client is used to interact with the **Amazon Simple Email Service (Amazon SES)**.
 *
 * @method \MageBackup\Aws\Result cloneReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise cloneReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createReceiptFilter(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createReceiptFilterAsync(array $args = [])
 * @method \MageBackup\Aws\Result createReceiptRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createReceiptRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result createReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIdentityPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIdentityPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteReceiptFilter(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteReceiptFilterAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteReceiptRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteReceiptRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteVerifiedEmailAddress(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteVerifiedEmailAddressAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeActiveReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeActiveReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeReceiptRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeReceiptRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityDkimAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityDkimAttributesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityMailFromDomainAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityMailFromDomainAttributesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityNotificationAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityNotificationAttributesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityPolicies(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityPoliciesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityVerificationAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityVerificationAttributesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSendQuota(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSendQuotaAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSendStatistics(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSendStatisticsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIdentities(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIdentitiesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIdentityPolicies(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIdentityPoliciesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listReceiptFilters(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listReceiptFiltersAsync(array $args = [])
 * @method \MageBackup\Aws\Result listReceiptRuleSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listReceiptRuleSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listVerifiedEmailAddresses(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listVerifiedEmailAddressesAsync(array $args = [])
 * @method \MageBackup\Aws\Result putIdentityPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putIdentityPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result reorderReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise reorderReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendBounce(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendBounceAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendEmail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendEmailAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendRawEmail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendRawEmailAsync(array $args = [])
 * @method \MageBackup\Aws\Result setActiveReceiptRuleSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setActiveReceiptRuleSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityDkimEnabled(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityDkimEnabledAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityFeedbackForwardingEnabled(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityFeedbackForwardingEnabledAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityMailFromDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityMailFromDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityNotificationTopic(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityNotificationTopicAsync(array $args = [])
 * @method \MageBackup\Aws\Result setReceiptRulePosition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setReceiptRulePositionAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateReceiptRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateReceiptRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result verifyDomainDkim(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise verifyDomainDkimAsync(array $args = [])
 * @method \MageBackup\Aws\Result verifyDomainIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise verifyDomainIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result verifyEmailAddress(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise verifyEmailAddressAsync(array $args = [])
 * @method \MageBackup\Aws\Result verifyEmailIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise verifyEmailIdentityAsync(array $args = [])
 */
class SesClient extends \MageBackup\Aws\AwsClient
{
    /**
     * Create an SMTP password for a given IAM user's credentials.
     *
     * The SMTP username is the Access Key ID for the provided credentials.
     *
     * @link http://docs.aws.amazon.com/ses/latest/DeveloperGuide/smtp-credentials.html#smtp-credentials-convert
     *
     * @param CredentialsInterface $creds
     *
     * @return string
     */
    public static function generateSmtpPassword(CredentialsInterface $creds)
    {
        static $version = "\x02";
        static $algo = 'sha256';
        static $message = 'SendRawEmail';
        $signature = hash_hmac($algo, $message, $creds->getSecretKey(), true);

        return base64_encode($version . $signature);
    }
}
