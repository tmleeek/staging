<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Alternatelang_Model_System_Config_Model_License extends Mage_Core_Model_Config_Data {

    public function afterLoad() 
    {
       $data = call_user_func(str_rot13('onfr64_qrpbqr'), "JG1vZHVsZSA9ICdNYWdtb2R1bGVzX0FsdGVybmF0ZWxhbmcnOyAkbW9kdWxlX3ZlcnNpb24gPSAnTWFnbW9kdWxzX0FsdEhyZWY2MTE5LTAyJzsgJG1vZHVsZV9wYXRoID0gJ2FsdGVybmF0ZWxhbmcvZ2VuZXJhbC8nOyAkbW9kdWxlX3NlcnZlciA9IHN0cl9yZXBsYWNlKCd3d3cuJywgJycsICRfU0VSVkVSWydIVFRQX0hPU1QnXSk7ICRtb2R1bGVfaW5zdGFsbGVkID0gTWFnZTo6Z2V0Q29uZmlnKCktPmdldE5vZGUoKS0+bW9kdWxlcy0+TWFnbW9kdWxlc19BbHRlcm5hdGVsYW5nLT52ZXJzaW9uOyByZXR1cm4gYmFzZTY0X2VuY29kZShiYXNlNjRfZW5jb2RlKGJhc2U2NF9lbmNvZGUoJG1vZHVsZSAuICc7JyAuICRtb2R1bGVfdmVyc2lvbiAuICc7JyAuICRtb2R1bGVfaW5zdGFsbGVkIC4gJzsnIC4gdHJpbShNYWdlOjpnZXRNb2RlbCgnY29yZS9jb25maWdfZGF0YScpLT5sb2FkKCRtb2R1bGVfcGF0aCAuICdsaWNlbnNlX2tleScsICdwYXRoJyktPmdldFZhbHVlKCkpIC4gJzsnIC4gJG1vZHVsZV9zZXJ2ZXIgLiAnOycgLiBNYWdlOjpnZXRVcmwoKSAuICc7JyAuIE1hZ2U6OmdldFNpbmdsZXRvbignYWRtaW4vc2Vzc2lvbicpLT5nZXRVc2VyKCktPmdldEVtYWlsKCkgLiAnOycgLiBNYWdlOjpnZXRTaW5nbGV0b24oJ2FkbWluL3Nlc3Npb24nKS0+Z2V0VXNlcigpLT5nZXROYW1lKCkgLiAnOycgLiAkX1NFUlZFUlsnU0VSVkVSX0FERFInXSkpKTs=");
	   $this->setValue(eval($data));
    }

    static function isEnabled() 
    {
		return eval(call_user_func(str_rot13('onfr64_qrpbqr'), "JG1vZHVsZV92ZXJzaW9uID0gJ01hZ21vZHVsc19BbHRIcmVmNjExOS0wMic7ICRtb2R1bGVfcGF0aCA9ICdhbHRlcm5hdGVsYW5nL2dlbmVyYWwvJzsgJG1vZHVsZV9zZXJ2ZXIgPSBzdHJfcmVwbGFjZSgnd3d3LicsICcnLCAkX1NFUlZFUlsnSFRUUF9IT1NUJ10pOyAka2V5ID0gdHJpbShNYWdlOjpnZXRNb2RlbCgnY29yZS9jb25maWdfZGF0YScpLT5sb2FkKCRtb2R1bGVfcGF0aCAuICdsaWNlbnNlX2tleScsICdwYXRoJyktPmdldFZhbHVlKCkpOyAkZ2VuX2tleSA9IHNoYTEoc2hhMSgkbW9kdWxlX3ZlcnNpb24gLiAnX21hZ18nIC4gJG1vZHVsZV9zZXJ2ZXIpKTsgJHN0cmluZyA9IGV4cGxvZGUoJy0nLCAka2V5KTsgaWYoJHN0cmluZ1swXSA9PSAnd2NkJykgeyAka2V5ID0gJHN0cmluZ1sxXTsgJG1vZHVsZV9zZXJ2ZXIgPSBhcnJheV9yZXZlcnNlKGV4cGxvZGUoJy4nLCAkbW9kdWxlX3NlcnZlcikpOyAkbW9kdWxlX3NlcnZlciA9ICRtb2R1bGVfc2VydmVyWzFdIC4gJy4nIC4gJG1vZHVsZV9zZXJ2ZXJbMF07ICRnZW5fa2V5ID0gc2hhMShzaGExKCRtb2R1bGVfdmVyc2lvbiAuICdfbWFnXycgLiAkbW9kdWxlX3NlcnZlcikpOyB9IGlmKCRnZW5fa2V5ICE9ICRrZXkpIHsgTWFnZTo6Z2V0Q29uZmlnKCktPnNhdmVDb25maWcoJG1vZHVsZV9wYXRoIC4gJ2VuYWJsZWQnLCAwKTsgTWFnZTo6Z2V0Q29uZmlnKCktPmNsZWFuQ2FjaGUoKTsgTWFnZTo6Z2V0U2luZ2xldG9uKCdhZG1pbmh0bWwvc2Vzc2lvbicpLT5hZGRFcnJvcihNYWdlOjpoZWxwZXIoJ2FsdGVybmF0ZWxhbmcnKS0+X18oIlRoZSBleHRlbnNpb24gY291bGRuJ3QgYmUgZW5hYmxlZC4gUGxlYXNlIG1ha2Ugc3VyZSB5b3UgYXJlIHVzaW5nIGEgdmFsaWQgbGljZW5zZSBrZXkuIikpOyByZXR1cm4gZmFsc2U7IH0gZWxzZSB7IHJldHVybiB0cnVlOyB9"));
    }
    
}
