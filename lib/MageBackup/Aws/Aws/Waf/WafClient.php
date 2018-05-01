<?php
namespace MageBackup\Aws\Waf;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS WAF** service.
 *
 * @method \MageBackup\Aws\Result createByteMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createByteMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createIPSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createIPSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result createSizeConstraintSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createSizeConstraintSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createSqlInjectionMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createSqlInjectionMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createWebACL(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createWebACLAsync(array $args = [])
 * @method \MageBackup\Aws\Result createXssMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createXssMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteByteMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteByteMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIPSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIPSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteSizeConstraintSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteSizeConstraintSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteSqlInjectionMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteSqlInjectionMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteWebACL(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteWebACLAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteXssMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteXssMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getByteMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getByteMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getChangeToken(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getChangeTokenAsync(array $args = [])
 * @method \MageBackup\Aws\Result getChangeTokenStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getChangeTokenStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIPSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIPSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSampledRequests(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSampledRequestsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSizeConstraintSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSizeConstraintSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSqlInjectionMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSqlInjectionMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getWebACL(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getWebACLAsync(array $args = [])
 * @method \MageBackup\Aws\Result getXssMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getXssMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result listByteMatchSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listByteMatchSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIPSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIPSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRules(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRulesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listSizeConstraintSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listSizeConstraintSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listSqlInjectionMatchSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listSqlInjectionMatchSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listWebACLs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listWebACLsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listXssMatchSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listXssMatchSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateByteMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateByteMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateIPSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateIPSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateSizeConstraintSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateSizeConstraintSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateSqlInjectionMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateSqlInjectionMatchSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateWebACL(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateWebACLAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateXssMatchSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateXssMatchSetAsync(array $args = [])
 */
class WafClient extends AwsClient {}
