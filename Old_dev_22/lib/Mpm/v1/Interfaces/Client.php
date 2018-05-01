<?php
/**
 * ApiClient Interface. Provides classmap for GatewayClient components
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @package \Mpm\GatewayClient\Interfaces
 */
namespace Mpm\GatewayClient\Interfaces;

interface ApiClient
{
    /**
     * Singleton constructor
     * @param void
     * @return Mpm\GatewayClient\Client Class instance
     */
    public static function getInstance();
}