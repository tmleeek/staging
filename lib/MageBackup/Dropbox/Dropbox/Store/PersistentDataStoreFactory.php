<?php
namespace MageBackup\Dropbox\Store;

use InvalidArgumentException;

/**
 * Thanks to Facebook
 *
 * @link https://developers.facebook.com/docs/php/PersistentDataInterface
 */
class PersistentDataStoreFactory
{
    /**
     * Make Persistent Data Store
     *
     * @param null|string|\MageBackup\Dropbox\Store\PersistentDataStoreInterface $store
     *
     * @throws InvalidArgumentException
     *
     * @return \MageBackup\Dropbox\Store\PersistentDataStoreInterface
     */
    public static function makePersistentDataStore($store = null)
    {
        if (is_null($store) || $store === 'session') {
            return new SessionPersistentDataStore();
        }

        if ($store instanceof PersistentDataStoreInterface) {
            return $store;
        }

        throw new InvalidArgumentException('The persistent data store must be set to null, "session" or be an instance of use \MageBackup\Dropbox\Store\PersistentDataStoreInterface');
    }
}
