<?php
namespace Mpm\GatewayClient;

class Cache
{

    public static function get($key, $ttl)
    {
        $filename = self::getFilename($key);
        if($filename === false) {
            return false;
        }

        if(!file_exists($filename) || filemtime($filename) < (time() - $ttl)) {
            return false;
        }

        return unserialize(file_get_contents($filename));
    }

    public static function set($key, $content)
    {
        $filename = self::getFilename($key);
        if($filename === false) {
            return false;
        }

        file_put_contents($filename, serialize($content));
    }

    private static function getFilename($key)
    {
        if(!is_writable(sys_get_temp_dir())) {
            return false;
        }

        if(!is_dir(sys_get_temp_dir().'/smart_price')) {
            mkdir(sys_get_temp_dir().'/smart_price', 0777, true);
        }

        return sys_get_temp_dir().'/smart_price/'.$key;
    }
}